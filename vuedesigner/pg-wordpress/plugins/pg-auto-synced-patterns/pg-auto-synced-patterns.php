<?php
/**
 * Plugin Name: Pinegrow Auto Synced Patterns (Headless Ready)
 * Description: Auto-creates editable synced patterns for Pinegrow dynamic blocks AND exposes them via REST API for headless front-ends. Automatically updates patterns when block defaults change using a version field, without overwriting client customizations. Includes a "Content Creator Admin" role restricted to Posts/Patterns/Media/Comments/Profile with full Cloudinary removal. Patterns are marked unsynced so client edits persist.
 * Version: 12.9
 */

/**
 * ---------------------------------------------------------
 * 0. CREATE / SYNC "Content Creator Admin" ROLE
 * ---------------------------------------------------------
 * - Clones Administrator capabilities into role "content_creator_admin"
 * - Keeps caps updated if Administrator gains new caps
 */
add_action('init', function () {
  $admin = get_role('administrator');
  if (!$admin) {
    return;
  }

  $admin_caps = $admin->capabilities;

  // Create role if it doesn't exist
  if (!get_role('content_creator_admin')) {
    add_role('content_creator_admin', 'Content Creator Admin', $admin_caps);
  } else {
    // Sync capabilities with Administrator (add any new caps)
    $role = get_role('content_creator_admin');
    if ($role) {
      foreach ($admin_caps as $cap => $grant) {
        if ($grant) {
          $role->add_cap($cap);
        }
      }
    }
  }
});


/**
 * ---------------------------------------------------------
 * 1. CONFIGURE wp_block FOR PUBLIC REST + EDITOR (v12.5 CONFIG)
 * ---------------------------------------------------------
 * This is the configuration that worked for your headless use case:
 * - public = true / publicly_queryable = true
 * - show_ui = true
 * - show_in_rest = true, rest_base = 'blocks'
 * - map_meta_cap = true, capability_type = 'wp_block'
 */
add_action('init', function () {
  global $wp_post_types;
  if (!isset($wp_post_types['wp_block'])) {
    return;
  }

  $pt = $wp_post_types['wp_block'];

  // HEADLESS-FRIENDLY PUBLIC CONFIG (v12.5 behaviour)
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
  $pt->capability_type = 'wp_block';
}, 15);


/**
 * ---------------------------------------------------------
 * 2. AUTO-CREATE / VERSIONED-UPDATE PINEGROW PATTERNS
 * ---------------------------------------------------------
 *
 * Based on your v12.5 logic:
 * - Looks at:  /theme/blocks/<slug>/<slug>_register.php
 * - Uses 'version' from args (e.g. 'version' => '1.0.34')
 * - If pattern doesn't exist → create it from defaults
 * - If pattern exists and version changed → update it
 *   · merges new defaults with client edits (client wins)
 * - If version is unchanged → DO NOT TOUCH pattern
 * - Marks patterns as "unsynced" so core/theme don't override edits
 */
add_action('init', 'pg_ap_auto_synced_patterns_init', 20);

function pg_ap_auto_synced_patterns_init()
{
  $blocks_dir = get_stylesheet_directory() . '/blocks/';
  if (!is_dir($blocks_dir)) {
    return;
  }

  $folders = array_filter(glob($blocks_dir . '*'), 'is_dir');
  if (empty($folders)) {
    return;
  }

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


/**
 * ---------------------------------------------------------
 * 3. Parse Pinegrow register file to extract block args
 * ---------------------------------------------------------
 */
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


/**
 * ---------------------------------------------------------
 * 4. Extract JSON attributes from block comment
 * ---------------------------------------------------------
 * Supports both:
 *   <!-- wp:ns/block {"a":"b"} /-->
 *   <!-- wp:ns/block {"a":"b"} --> ... <!-- /wp:ns/block -->
 */
function pg_ap_extract_attributes_from_block_comment($content)
{
  if (preg_match('/<!--\s*wp:[^ ]]+\s+({.*?})\s*(?:\/)?-->/', $content, $m)) {
    $json = json_decode($m[1], true);
    return is_array($json) ? $json : [];
  }

  return [];
}


/**
 * ---------------------------------------------------------
 * 5. Add Patterns + Profile to menu (for convenience)
 * ---------------------------------------------------------
 */
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


/**
 * ---------------------------------------------------------
 * 6. Hide all other menus for Content Creator Admin
 * ---------------------------------------------------------
 * For users with role "content_creator_admin", only show:
 * - Dashboard
 * - Posts
 * - Media
 * - Comments
 * - Patterns (wp_block)
 * - Profile
 * Everything else is hidden (including Cloudinary).
 */
add_action('admin_menu', function () {
  $user = wp_get_current_user();
  if (!$user || !in_array('content_creator_admin', (array) $user->roles, true)) {
    return;
  }

  global $menu, $submenu;

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

  // CLOUDINARY REMOVAL — FULL + GUARANTEED
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
