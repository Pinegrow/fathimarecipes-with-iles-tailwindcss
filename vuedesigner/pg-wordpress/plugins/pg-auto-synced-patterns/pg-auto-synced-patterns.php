<?php
/**
 * Plugin Name: Pinegrow Auto Synced Patterns (Content Creator Admin)
 * Description: Description: Auto-creates editable synced patterns for Pinegrow dynamic blocks AND exposes them via REST API for headless front-ends. Automatically updates patterns when block defaults change without overwriting client customizations. Includes "Content Creator Admin" role restricted to Posts/Patterns/Media/Comments/Profile.
 * Version: 4.4
 */

//
// 0. Clone Administrator caps into “content_creator” role
// ------------------------------------------------------
// Assign your clients the "Content Creator" role.
//
add_action('init', function () {
  $admin = get_role('administrator');
  if (!$admin) {
    return;
  }

  $admin_caps = $admin->capabilities;

  if (!get_role('content_creator')) {
    add_role('content_creator', 'Content Creator', $admin_caps);
  } else {
    $role = get_role('content_creator');
    if ($role) {
      foreach ($admin_caps as $cap => $grant) {
        if ($grant) {
          $role->add_cap($cap);
        }
      }
    }
  }
});


//
// 1. Configure wp_block for public REST access + editor compatibility
// -------------------------------------------------------------------
// - public = true  → allows unauthenticated GET /wp-json/wp/v2/blocks
// - publicly_queryable = true
// - show_in_rest = true, rest_base = 'blocks'
// - map_meta_cap = true
//
add_action('init', function () {
  global $wp_post_types;
  if (!isset($wp_post_types['wp_block']))
    return;

  $pt = $wp_post_types['wp_block'];

  $pt->public = true;
  $pt->publicly_queryable = true;
  $pt->exclude_from_search = false;

  $pt->show_ui = true;
  $pt->show_in_rest = true;
  $pt->show_in_menu = true;
  $pt->show_in_admin_bar = true;
  $pt->show_in_nav_menus = false;

  if (empty($pt->rest_base)) {
    $pt->rest_base = 'blocks';
  }
  if (empty($pt->rest_controller_class)) {
    $pt->rest_controller_class = 'WP_REST_Posts_Controller';
  }

  $pt->map_meta_cap = true;
}, 15);


//
// 2. Auto-create / versioned-update Pinegrow patterns
// ---------------------------------------------------
// - Looks at /theme/blocks/<slug>/<slug>_register.php
// - Uses 'version' from the args array (e.g. 'version' => '1.0.34')
// - If pattern doesn't exist → create it
// - If pattern exists and version changed → update it (merge defaults + client edits)
// - If version is the same → DO NOT TOUCH (client edits persist)
// - Marks patterns as "unsynced" so theme doesn't override client edits.
//
add_action('init', 'pg_ap_auto_synced_patterns_init', 20);

function pg_ap_auto_synced_patterns_init()
{
  $blocks_dir = get_stylesheet_directory() . '/blocks/';
  if (!is_dir($blocks_dir)) {
    return;
  }

  $folders = array_filter(glob($blocks_dir . '*'), 'is_dir');

  foreach ($folders as $folder) {

    $slug = basename($folder);
    $register_file = $folder . '/' . $slug . '_register.php';

    if (!file_exists($register_file)) {
      continue;
    }

    $args = pg_ap_parse_pinegrow_register_file($register_file);
    if (!$args || empty($args['name'])) {
      continue;
    }

    $block_name = $args['name']; // e.g. garden-mate/hero-seasonal
    $title = !empty($args['title']) ? wp_strip_all_tags($args['title']) : ucfirst($slug);
    $attributes = $args['attributes'] ?? [];

    // Version from Pinegrow register file (e.g. '1.0.34')
    $block_version = !empty($args['version']) ? (string) $args['version'] : '';

    // Extract defaults from Pinegrow attributes
    $default_attrs = [];
    foreach ($attributes as $key => $config) {
      if (is_array($config) && array_key_exists('default', $config)) {
        $default_attrs[$key] = $config['default'];
      }
    }

    // Check if pattern already exists
    $existing = get_page_by_path($slug, OBJECT, 'wp_block');

    // ------------------------------------------------
    // A. UPDATE EXISTING PATTERN (ONLY IF VERSION BUMPED)
    // ------------------------------------------------
    if ($existing) {

      // If no version defined, never auto-update existing pattern (play it safe)
      if ($block_version === '') {
        continue;
      }

      $stored_version = get_post_meta($existing->ID, '_pg_synced_version', true);

      // If version hasn't changed → don't overwrite client's edits
      if ($stored_version === $block_version) {
        continue;
      }

      // Version changed → update pattern based on latest defaults + current client edits
      $old_json = pg_ap_extract_attributes_from_block_comment($existing->post_content);
      if (!is_array($old_json)) {
        $old_json = [];
      }

      // Merge: new defaults + client overrides (client wins for existing keys)
      $merged = array_merge($default_attrs, $old_json);

      $json = json_encode($merged);
      $block_open = "<!-- wp:$block_name $json -->";
      $block_close = "<!-- /wp:$block_name -->";
      $content = $block_open . $block_close;

      wp_update_post([
        'ID' => $existing->ID,
        'post_content' => $content,
        'post_title' => $title, // keep title in sync too
      ]);

      // Mark as unsynced and record version so we don't update again until version changes
      update_post_meta($existing->ID, '_wp_pattern_sync_status', 'unsynced');
      delete_post_meta($existing->ID, '_wp_block_theme');
      delete_post_meta($existing->ID, '_wp_pattern_source');
      update_post_meta($existing->ID, '_pg_synced_version', $block_version);

      continue;
    }

    // ----------------------------------
    // B. CREATE NEW PATTERN (FIRST TIME)
    // ----------------------------------
    $json = json_encode($default_attrs);
    $block_open = "<!-- wp:$block_name $json -->";
    $block_close = "<!-- /wp:$block_name -->";
    $content = $block_open . $block_close;

    $pattern_id = wp_insert_post([
      'post_title' => $title,
      'post_name' => $slug,
      'post_type' => 'wp_block',
      'post_status' => 'publish',
      'post_content' => $content,
    ]);

    if ($pattern_id && !is_wp_error($pattern_id)) {
      // Make it user-editable (unsynced)
      update_post_meta($pattern_id, '_wp_pattern_sync_status', 'unsynced');
      delete_post_meta($pattern_id, '_wp_block_theme');
      delete_post_meta($pattern_id, '_wp_pattern_source');

      if ($block_version !== '') {
        update_post_meta($pattern_id, '_pg_synced_version', $block_version);
      }
    }
  }
}


//
// 3. Parse Pinegrow register file to extract block args
// -----------------------------------------------------
function pg_ap_parse_pinegrow_register_file($file_path)
{
  $php = file_get_contents($file_path);
  if ($php === false)
    return null;

  $needle = 'PG_Blocks_v4::register_block_type';
  $pos = strpos($php, $needle);
  if ($pos === false)
    return null;

  $pos = strpos($php, '(', $pos);
  if ($pos === false)
    return null;

  $len = strlen($php);
  $depth = 0;
  $start = null;
  $end = null;

  for ($i = $pos; $i < $len; $i++) {
    $ch = $php[$i];

    if ($ch === '(') {
      $depth++;
      if ($depth === 1)
        $start = $i + 1;
    } elseif ($ch === ')') {
      $depth--;
      if ($depth === 0) {
        $end = $i;
        break;
      }
    }
  }

  if ($start === null || $end === null)
    return null;

  $inner = trim(substr($php, $start, $end - $start));
  if (stripos($inner, 'array') !== 0)
    return null;

  try {
    return eval ('return ' . $inner . ';');
  } catch (\Throwable $e) {
    error_log('PG AP parse error: ' . $file_path . ': ' . $e->getMessage());
    return null;
  }
}


//
// 4. Extract JSON attributes from block comment
// --------------------------------------------
// Supports both:
//   <!-- wp:ns/block {"a":"b"} /-->
//   <!-- wp:ns/block {"a":"b"} --> ... <!-- /wp:ns/block -->
//
function pg_ap_extract_attributes_from_block_comment($content)
{
  if (preg_match('/<!--\s*wp:[^ ]]+\s+({.*?})\s*(?:\/)?-->/', $content, $m)) {
    $json = json_decode($m[1], true);
    return is_array($json) ? $json : [];
  }

  return [];
}


//
// 5. Add Patterns + Profile to menu (for convenience)
// ---------------------------------------------------
add_action('admin_menu', function () {
  // Patterns top-level (wp_block)
  if (current_user_can('edit_posts')) {
    add_menu_page(
      'Patterns',
      'Patterns',
      'edit_posts',
      'edit.php?post_type=wp_block',
      '',
      'dashicons-layout',
      5
    );
  }

  // Direct Profile entry
  if (current_user_can('read')) {
    add_menu_page(
      'Profile',
      'Profile',
      'read',
      'profile.php',
      '',
      'dashicons-admin-users',
      80
    );
  }
}, 20);


//
// 6. Hide all other menus for Content Creator
// -------------------------------------------
// For users with role "content_creator", only show:
// - Dashboard
// - Posts
// - Media
// - Comments
// - Patterns (wp_block)
// - Profile
// Everything else is hidden (including AIP).
//
add_action('admin_menu', function () {
  $user = wp_get_current_user();
  if (!in_array('content_creator', (array) $user->roles, true)) {
    return;
  }

  global $menu;

  $allowed = [
    'index.php',                   // Dashboard
    'edit.php',                    // Posts
    'upload.php',                  // Media
    'edit-comments.php',           // Comments
    'edit.php?post_type=wp_block', // Patterns
    'profile.php',                 // Profile
  ];

  foreach ($menu as $index => $item) {
    $slug = $item[2] ?? '';
    $label = wp_strip_all_tags($item[0] ?? '');

    if (!in_array($slug, $allowed, true)) {
      remove_menu_page($slug);
      continue;
    }

    // Hide any that mention "AIP" in their label, just in case
    if (stripos($label, 'AIP') !== false) {
      remove_menu_page($slug);
    }
  }
}, 999);
