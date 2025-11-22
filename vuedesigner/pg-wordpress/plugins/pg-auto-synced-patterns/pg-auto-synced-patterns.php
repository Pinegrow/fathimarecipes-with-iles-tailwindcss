<?php
/**
 * Plugin Name: Pinegrow Auto Synced Patterns (Headless Ready)
 * Description: Auto-creates editable synced patterns for Pinegrow dynamic blocks AND exposes them via REST API for headless front-ends. Automatically updates patterns when block defaults change using a version field, without overwriting client customizations. Includes a "Content Creator Admin" role restricted to Posts/Patterns/Media/Comments/Profile with full Cloudinary removal. Patterns are marked unsynced so client edits persist.
 * Version: 12.7
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
 * 1. MAKE SYNCED PATTERNS (wp_block) PUBLIC + REST + UI
 * ---------------------------------------------------------
 * - public = true              → wp_block is publicly visible
 * - publicly_queryable = true  → can be queried on front-end
 * - show_ui = true             → shows in WP Admin
 * - show_in_menu = true        → adds to admin menu
 * - show_in_rest = true        → exposes via REST API
 * - rest_base = blocks         → /wp-json/wp/v2/blocks
 * - map_meta_cap = true        → proper capability mapping
 */
add_action('init', function () {
  global $wp_post_types;

  if (!isset($wp_post_types['wp_block'])) {
    return;
  }

  $pt = $wp_post_types['wp_block'];

  $pt->public = true;
  $pt->publicly_queryable = true;
  $pt->exclude_from_search = false;

  $pt->show_ui = true;
  $pt->show_in_menu = true;
  $pt->show_in_admin_bar = true;
  $pt->show_in_nav_menus = false;

  $pt->show_in_rest = true;
  if (empty($pt->rest_base)) {
    $pt->rest_base = 'blocks';
  }
  if (empty($pt->rest_controller_class)) {
    $pt->rest_controller_class = 'WP_REST_Posts_Controller';
  }

  $pt->map_meta_cap = true;
}, 15);


/**
 * ---------------------------------------------------------
 * 2. AUTO-CREATE / VERSIONED-UPDATE PINEGROW PATTERNS
 * ---------------------------------------------------------
 *
 * - Looks at:  /theme/blocks/<slug>/<slug>_register.php
 * - Uses 'version' from args (e.g. 'version' => '1.0.34')
 * - If pattern doesn't exist → create it from defaults
 * - If pattern exists and version changed → update it
 *   · merges new defaults with client edits (client wins)
 * - If version is unchanged → DO NOT TOUCH pattern
 * - Marks patterns as "unsynced" so core/theme don't override edits
 */
add_action('init', 'pg_auto_synced_patterns_init', 20);

function pg_auto_synced_patterns_init()
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

    $args = pg_parse_pinegrow_register_file($register_file);
    if (!$args || !is_array($args) || empty($args['name'])) {
      continue;
    }

    $block_name = $args['name']; // e.g. garden-mate/hero-seasonal
    $title = !empty($args['title']) ? wp_strip_all_tags($args['title']) : ucfirst($slug);
    $attributes = isset($args['attributes']) && is_array($args['attributes']) ? $args['attributes'] : [];
    $block_version = !empty($args['version']) ? (string) $args['version'] : '';

    // Extract default attributes from Pinegrow attributes config
    $default_attrs = [];
    foreach ($attributes as $key => $config) {
      if (is_array($config) && array_key_exists('default', $config)) {
        $default_attrs[$key] = $config['default'];
      }
    }

    // Look for existing pattern
    $existing = get_page_by_path($slug, OBJECT, 'wp_block');

    // --------------------------------------------
    // A. UPDATE EXISTING PATTERN (IF VERSION BUMPED)
    // --------------------------------------------
    if ($existing) {

      // If no version defined, never auto-update (keep client edits safe)
      if ($block_version === '') {
        continue;
      }

      $stored_version = get_post_meta($existing->ID, '_pg_synced_version', true);

      // Version unchanged → skip (do not overwrite client edits)
      if ($stored_version === $block_version) {
        continue;
      }

      // Version changed → update based on new defaults + current client edits
      $old_json = pg_extract_attributes_from_block_comment($existing->post_content);
      if (!is_array($old_json)) {
        $old_json = [];
      }

      // Merge: new defaults + client overrides (client wins on existing keys)
      $merged = array_merge($default_attrs, $old_json);

      $json = json_encode($merged);
      $block_open = "<!-- wp:$block_name $json -->";
      $block_close = "<!-- /wp:$block_name -->";
      $content = $block_open . $block_close;

      wp_update_post([
        'ID' => $existing->ID,
        'post_title' => $title,
        'post_content' => $content,
      ]);

      // Mark as unsynced and record version
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
 * 3. PARSE PINEGROW REGISTER FILE SAFELY
 * ---------------------------------------------------------
 * Parses the args array from:
 *   PG_Blocks_v4::register_block_type( array( ... ) );
 */
function pg_parse_pinegrow_register_file($file_path)
{
  $php = file_get_contents($file_path);
  if ($php === false) {
    return null;
  }

  $needle = 'PG_Blocks_v4::register_block_type';
  $pos = strpos($php, $needle);
  if ($pos === false) {
    return null;
  }

  $pos = strpos($php, '(', $pos);
  if ($pos === false) {
    return null;
  }

  $len = strlen($php);
  $depth = 0;
  $start = null;
  $end = null;

  for ($i = $pos; $i < $len; $i++) {
    $ch = $php[$i];

    if ($ch === '(') {
      $depth++;
      if ($depth === 1) {
        $start = $i + 1;
      }
    } elseif ($ch === ')') {
      $depth--;
      if ($depth === 0) {
        $end = $i;
        break;
      }
    }
  }

  if ($start === null || $end === null) {
    return null;
  }

  $inner = trim(substr($php, $start, $end - $start));
  if (stripos($inner, 'array') !== 0) {
    return null;
  }

  try {
    return eval ('return ' . $inner . ';');
  } catch (\Throwable $e) {
    error_log('Pattern parse error in ' . $file_path . ': ' . $e->getMessage());
    return null;
  }
}


/**
 * ---------------------------------------------------------
 * 4. EXTRACT ATTRIBUTES FROM BLOCK COMMENT
 * ---------------------------------------------------------
 * Supports both:
 *   <!-- wp:ns/block {"a":"b"} /-->
 *   <!-- wp:ns/block {"a":"b"} --> ... <!-- /wp:ns/block -->
 */
function pg_extract_attributes_from_block_comment($content)
{
  if (preg_match('/<!--\s*wp:[^ ]]+\s+({.*?})\s*(?:\/)?-->/', $content, $m)) {
    $json = json_decode($m[1], true);
    return is_array($json) ? $json : [];
  }

  return [];
}


/**
 * ---------------------------------------------------------
 * 5. ADD PATTERNS & PROFILE SHORTCUTS TO ADMIN MENU
 * ---------------------------------------------------------
 * - Adds a top-level "Patterns" menu (wp_block)
 * - Adds a top-level "Profile" menu
 */
add_action('admin_menu', function () {
  // Patterns top-level
  if (current_user_can('edit_posts')) {
    add_menu_page(
      'Patterns',
      'Patterns',
      'edit_posts',
      'edit.php?post_type=wp_block',
      '',
      'dashicons-layout',
      21
    );
  }

  // Direct Profile
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
 * 6. LIMIT MENUS FOR CONTENT CREATOR ADMIN (Option A)
 * ---------------------------------------------------------
 * For users with role "content_creator_admin", only show:
 * - Dashboard
 * - Posts
 * - Media
 * - Comments
 * - Patterns (wp_block)
 * - Profile
 * Everything else is hidden.
 * Also includes FULL Cloudinary removal.
 */
add_action('admin_menu', function () {
  $user = wp_get_current_user();
  if (!$user || !in_array('content_creator_admin', (array) $user->roles, true)) {
    return;
  }

  global $menu, $submenu;

  // Allow only these slugs
  $allowed = [
    'index.php',                   // Dashboard
    'edit.php',                    // Posts
    'upload.php',                  // Media
    'edit-comments.php',           // Comments
    'edit.php?post_type=wp_block', // Patterns
    'profile.php',                 // Profile
  ];

  // Remove any top-level menu not in allowed or that mentions Cloudinary
  foreach ($menu as $index => $item) {
    $slug = $item[2] ?? '';
    $label = wp_strip_all_tags($item[0] ?? '');

    // Cloudinary removal by label
    if ($label && stripos($label, 'cloudinary') !== false) {
      remove_menu_page($slug);
      continue;
    }

    if (!in_array($slug, $allowed, true)) {
      remove_menu_page($slug);
    }
  }

  // Remove any submenu containing "Cloudinary" in the label
  if (is_array($submenu)) {
    foreach ($submenu as $parent_slug => $items) {
      foreach ($items as $i => $sub_item) {
        if (isset($sub_item[0]) && stripos($sub_item[0], 'cloudinary') !== false) {
          unset($submenu[$parent_slug][$i]);
        }
      }
    }
  }

  // Remove by known Cloudinary slugs (covers multiple versions)
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
 * 7. CLEAN UP APPEARANCE SUBMENUS FOR CONTENT CREATOR ADMIN
 * ---------------------------------------------------------
 */
add_action('admin_init', function () {
  $user = wp_get_current_user();
  if (!$user || !in_array('content_creator_admin', (array) $user->roles, true)) {
    return;
  }

  remove_submenu_page('themes.php', 'nav-menus.php');
  remove_submenu_page('themes.php', 'theme-editor.php');
  remove_submenu_page('themes.php', 'widgets.php');
  remove_submenu_page('themes.php', 'site-editor.php'); // FSE
});


/**
 * ---------------------------------------------------------
 * 8. HIDE USERS LIST FOR CONTENT CREATOR ADMIN (Profile Only)
 * ---------------------------------------------------------
 */
add_action('admin_menu', function () {
  $user = wp_get_current_user();
  if (!$user || !in_array('content_creator_admin', (array) $user->roles, true)) {
    return;
  }

  remove_menu_page('users.php');
}, 999);
