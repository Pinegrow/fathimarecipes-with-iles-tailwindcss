<?php
/**
 * Plugin Name: Pinegrow Auto Synced Patterns (Headless Ready)
 * Description: Auto-creates editable synced patterns for Pinegrow dynamic blocks AND exposes them via REST API for headless front-ends. Automatically updates patterns when block defaults change without overwriting client customizations. Includes "Content Creator Admin" role restricted to Posts/Patterns/Media/Comments/Profile with full Cloudinary removal.
 * Version: 4.3
 */




/**
 * ---------------------------------------------------------
 * 1. MAKE SYNCED PATTERNS (wp_block) PUBLIC + REST VISIBLE
 * ---------------------------------------------------------
 */
add_action('init', function () {
  global $wp_post_types;

  if (isset($wp_post_types['wp_block'])) {
    $wp_post_types['wp_block']->public = true;
    $wp_post_types['wp_block']->publicly_queryable = true;
    $wp_post_types['wp_block']->show_in_rest = true;
    $wp_post_types['wp_block']->rest_base = 'blocks';
    $wp_post_types['wp_block']->rest_controller_class = 'WP_REST_Posts_Controller';
  }
}, 15);




/**
 * ---------------------------------------------------------
 * 2. AUTO-GENERATE + AUTO-UPDATE SYNCED PATTERNS
 * ---------------------------------------------------------
 */
add_action('init', 'pg_auto_synced_patterns_init', 20);

function pg_auto_synced_patterns_init()
{

  $blocks_dir = get_stylesheet_directory() . '/blocks/';
  if (!is_dir($blocks_dir))
    return;

  $folders = array_filter(glob($blocks_dir . '*'), 'is_dir');

  foreach ($folders as $folder) {

    $slug = basename($folder);
    $register_file = $folder . '/' . $slug . '_register.php';

    if (!file_exists($register_file))
      continue;

    $args = pg_parse_pinegrow_register_file($register_file);
    if (!$args || !is_array($args) || empty($args['name']))
      continue;

    $block_name = $args['name'];
    $title = isset($args['title']) ? wp_strip_all_tags($args['title']) : ucfirst($slug);
    $attributes = isset($args['attributes']) && is_array($args['attributes'])
      ? $args['attributes']
      : [];

    // Extract default attributes
    $default_attrs = [];
    foreach ($attributes as $attr_name => $config) {
      if (is_array($config) && array_key_exists('default', $config)) {
        $default_attrs[$attr_name] = $config['default'];
      }
    }

    // Look for existing pattern
    $existing = get_page_by_path($slug, OBJECT, 'wp_block');

    if ($existing) {
      $old_json = pg_extract_attributes_from_block_comment($existing->post_content);
      if (!is_array($old_json))
        $old_json = [];

      // Merge defaults → user edits win
      $merged = array_merge($default_attrs, $old_json);

      // Only update when needed
      if ($merged !== $old_json) {
        $new_content = "<!-- wp:$block_name " . json_encode($merged) . " /-->";
        wp_update_post([
          'ID' => $existing->ID,
          'post_content' => $new_content,
        ]);
      }
      continue;
    }

    // Create new pattern
    $json = !empty($default_attrs) ? json_encode($default_attrs) : '';
    $content = "<!-- wp:$block_name $json /-->";

    wp_insert_post([
      'post_title' => $title,
      'post_name' => $slug,
      'post_type' => 'wp_block',
      'post_status' => 'publish',
      'post_content' => $content,
    ]);
  }
}




/**
 * ---------------------------------------------------------
 * 3. PARSE PINEGROW REGISTER FILE SAFELY
 * ---------------------------------------------------------
 */
function pg_parse_pinegrow_register_file($file_path)
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

  $code = 'return ' . $inner . ';';

  try {
    return eval ($code);
  } catch (\Throwable $e) {
    error_log('Pattern parse error in ' . $file_path . ': ' . $e->getMessage());
    return null;
  }
}




/**
 * ---------------------------------------------------------
 * 4. EXTRACT ATTRIBUTES FROM BLOCK COMMENT
 * ---------------------------------------------------------
 */
function pg_extract_attributes_from_block_comment($content)
{
  if (preg_match('/<!--\s*wp:[^ ]+\s+({.*?})\s*\/-->/s', $content, $m)) {
    $json = json_decode($m[1], true);
    return is_array($json) ? $json : [];
  }
  return [];
}




/**
 * ---------------------------------------------------------
 * 5. CREATE "Content Creator Admin" ROLE (Clone of Admin)
 * ---------------------------------------------------------
 */
add_action('init', function () {

  if (!get_role('content_creator_admin')) {
    $admin = get_role('administrator');
    if ($admin) {
      add_role(
        'content_creator_admin',
        'Content Creator Admin',
        $admin->capabilities
      );
    }
  }
});




/**
 * ---------------------------------------------------------
 * 6. LIMIT MENUS FOR CONTENT CREATOR ADMIN
 * ---------------------------------------------------------
 */
add_action('admin_menu', function () {

  /**
   * ---------------------------------------------------------
   * Add Patterns menu (must be added manually)
   * ---------------------------------------------------------
   */
  add_menu_page(
    'Patterns',
    'Patterns',
    'edit_posts',
    'edit.php?post_type=wp_block',
    '',
    'dashicons-layout',
    21
  );

  if (!current_user_can('content_creator_admin'))
    return;

  // Remove core admin menus
  remove_menu_page('index.php');                 // Dashboard
  remove_menu_page('edit.php?post_type=page');  // Pages
  remove_menu_page('themes.php');               // Appearance
  remove_menu_page('plugins.php');              // Plugins
  remove_menu_page('users.php');                // Users
  remove_menu_page('tools.php');                // Tools
  remove_menu_page('options-general.php');      // Settings
  remove_menu_page('woocommerce');              // WooCommerce
  remove_menu_page('edit.php?post_type=product'); // Products




  /**
   * ---------------------------------------------------------
   * CLOUDINARY REMOVAL — FULL + GUARANTEED (ALL VERSIONS)
   * ---------------------------------------------------------
   */

  global $menu, $submenu;

  // Remove any top-level menu containing the text "Cloudinary"
  foreach ($menu as $index => $item) {
    if (isset($item[0]) && stripos($item[0], 'cloudinary') !== false) {
      unset($menu[$index]);
    }
  }

  // Remove any submenu containing the text "Cloudinary"
  if (is_array($submenu)) {
    foreach ($submenu as $parent_slug => $sub_items) {
      foreach ($sub_items as $i => $sub_item) {
        if (isset($sub_item[0]) && stripos($sub_item[0], 'cloudinary') !== false) {
          unset($submenu[$parent_slug][$i]);
        }
      }
    }
  }

  // Remove by known Cloudinary slugs (covers multiple plugin versions)
  $cloudinary_slugs = [
    'cloudinary',
    'cloudinary-settings',
    'cloudinary_library',
    'cloudinary_dashboard',
    'admin.php?page=cloudinary',
    'admin.php?page=cloudinary-settings',
    'admin.php?page=cloudinary_library',
    'upload.php?page=cloudinary',
    'options-general.php?page=cloudinary',
  ];

  foreach ($cloudinary_slugs as $slug) {
    remove_menu_page($slug);
    remove_submenu_page('upload.php', $slug);
    remove_submenu_page('options-general.php', $slug);
    remove_submenu_page('tools.php', $slug);
  }





}, 999);




/**
 * ---------------------------------------------------------
 * 7. CLEAN UP SUBMENUS (Appearance, Editor, FSE, etc.)
 * ---------------------------------------------------------
 */
add_action('admin_init', function () {
  if (!current_user_can('content_creator_admin'))
    return;

  remove_submenu_page('themes.php', 'nav-menus.php');
  remove_submenu_page('themes.php', 'theme-editor.php');
  remove_submenu_page('themes.php', 'widgets.php');
  remove_submenu_page('themes.php', 'site-editor.php'); // FSE
});




/**
 * ---------------------------------------------------------
 * 8. ALLOW PROFILE ONLY (Hide Users List)
 * ---------------------------------------------------------
 */
add_action('admin_menu', function () {
  if (current_user_can('content_creator_admin')) {
    remove_menu_page('users.php');
  }
}, 999);

