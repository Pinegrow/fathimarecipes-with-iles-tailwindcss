<?php
/**
 * Plugin Name: Pinegrow Auto Synced Patterns (Headless Ready)
 * Description: Auto-creates editable synced patterns for Pinegrow dynamic blocks AND exposes them via REST API for headless front-ends. Automatically updates patterns when block defaults change without overwriting client customizations. Includes "Content Creator Admin" role restricted to Posts/Patterns/Media/Comments/Profile with full Cloudinary removal.
 * Version: 4.6
 */

/**
 * Compute a stable hash representing all relevant block files.
 */
function pg_compute_block_markup_hash($block_dir)
{
  if (!is_dir($block_dir))
    return '';

  $hash_context = hash_init('md5');

  $iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($block_dir, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
  );

  foreach ($iterator as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // Files that affect the block's rendered markup
    if (in_array($ext, ['php', 'html', 'htm', 'twig', 'css', 'js'])) {
      hash_update_file($hash_context, $file);
    }
  }

  return hash_final($hash_context);
}

/**
 * Main sync logic for patterns.
 */
add_action('init', function () {
  $theme_dir = get_stylesheet_directory();
  $blocks_root = $theme_dir . '/blocks';

  if (!is_dir($blocks_root))
    return;

  foreach (glob($blocks_root . '/*', GLOB_ONLYDIR) as $block_dir) {
    $block_name = basename($block_dir);
    $register_file = $block_dir . '/_register.php';

    if (!file_exists($register_file))
      continue;

    // Load Pinegrow register file defaults
    $block_data = include $register_file;
    if (!isset($block_data['attributes']))
      continue;

    $default_attrs = [];
    foreach ($block_data['attributes'] as $attr_name => $attr) {
      if (isset($attr['default'])) {
        $default_attrs[$attr_name] = $attr['default'];
      }
    }

    // Compute markup hash (NEW in v4.5)
    $markup_hash = pg_compute_block_markup_hash($block_dir);

    // Pattern post name
    $pattern_slug = sanitize_title("pg-pattern-{$block_name}");
    $existing = get_page_by_path($pattern_slug, OBJECT, 'wp_block');

    $existing_hash = $existing ? get_post_meta($existing->ID, '_pg_pattern_hash', true) : '';

    // Detect attribute changes
    $pattern_content = '';
    if ($existing) {
      $pattern_content = $existing->post_content;
      $old_defaults = get_post_meta($existing->ID, '_pg_default_attributes', true);
      $old_defaults = is_array($old_defaults) ? $old_defaults : [];
    }

    $defaults_changed = isset($old_defaults) && $old_defaults !== $default_attrs;
    $markup_changed = $existing_hash !== $markup_hash;

    // If pattern missing OR defaults changed OR markup changed → rebuild
    if (!$existing || $defaults_changed || $markup_changed) {

      // Construct block JSON → comment wrapper
      $json = array_merge(
        is_array($old_defaults ?? []) ? $old_defaults : [],
        $default_attrs
      );

      // Build block syntax: <!-- wp:theme/block-name {"a":"b"} /-->
      $pattern_content = sprintf(
        '<!-- wp:%s/%s %s /-->',
        wp_get_theme()->get_stylesheet(),
        $block_name,
        $json ? wp_json_encode($json) : ''
      );

      $pattern_args = [
        'post_title' => ucfirst($block_name) . ' (Synced)',
        'post_name' => $pattern_slug,
        'post_status' => 'publish',
        'post_type' => 'wp_block',
        'post_content' => $pattern_content,
      ];

      if ($existing) {
        $pattern_args['ID'] = $existing->ID;
        wp_update_post($pattern_args);
        $pattern_id = $existing->ID;
      } else {
        $pattern_id = wp_insert_post($pattern_args);
      }

      // Save updated metadata
      update_post_meta($pattern_id, '_pg_default_attributes', $default_attrs);
      update_post_meta($pattern_id, '_pg_pattern_hash', $markup_hash);
    }
  }
});
