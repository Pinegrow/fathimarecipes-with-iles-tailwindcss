<?php
/**
 * Plugin Name:       Pinegrow Auto Synced Patterns
 * Plugin URI: https://pinegrow.com/docs/vue/headless-wordpress
 * Description: Auto-creates editable synced patterns for Pinegrow dynamic blocks AND exposes them via REST API for headless front-ends. Automatically updates patterns when block defaults change without overwriting client customizations. Includes "Content Creator Admin" role restricted to Posts/Patterns/Media/Comments/Profile.
 * Version: 1.0.5
 * Requires at least: 5.5
 * Requires PHP: 5.3
 * Author: Pinegrow
 * Author URI: https://vuedesigner.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pg_auto_synced_patterns
 * Domain Path: /languages
*/
?><?php
$pinegrow_run_code = true;

/* Begin - Prevent broken project from crashing the Pinegrow editor */
if(defined('DOING_AJAX') && DOING_AJAX && !empty($_REQUEST['action']) && strpos($_REQUEST['action'], 'pinegrow_api') === 0) {
    $pinegrow_run_code = false; //do not run during Pinegrow API calls
}
if(strpos($_SERVER['REQUEST_URI'], '/wp-admin/admin.php?page=pinegrow-projects') === 0 || strpos($_SERVER['REQUEST_URI'], '/wp-login') === 0 || (strpos($_SERVER['REQUEST_URI'], '/wp-admin/plugins.php') === 0 && strpos($_SERVER['REQUEST_URI'], '/wp-admin/plugins.php?action=activate') === false)) {
    //do not load when editor is loading, during login and plugin manipulation in admin, except when plugin is being activated
    $pinegrow_run_code = false;
}

if(isset($_COOKIE['pinegrow_staging']) && file_exists( dirname( dirname( __FILE__ ) ) .  '/pinegrow_auto_synced_patterns_staging/pinegrow_auto_synced_patterns.php' ) ) {
    //exit if we are in the staging mode and the staging build exists
    $pinegrow_run_code = false;
}             
if( $pinegrow_run_code ) :

/* End - Prevent broken project from crashing the Pinegrow editor */            
?><?php


 

if ( ! function_exists( 'pinegrow_auto_synced_patterns_plugin_base_url' ) ) :
 
function pinegrow_auto_synced_patterns_plugin_base_url() {
    global $pinegrow_auto_synced_patterns_plugin_base_url_value;
    if(empty($pinegrow_auto_synced_patterns_plugin_base_url_value)) {
        $pinegrow_auto_synced_patterns_plugin_base_url_value = untrailingslashit( plugin_dir_url( __FILE__ ) );
    }
    return $pinegrow_auto_synced_patterns_plugin_base_url_value;
}

endif;

if ( ! function_exists( 'pinegrow_auto_synced_patterns_plugin_base_path' ) ) :
 
function pinegrow_auto_synced_patterns_plugin_base_path() {
    global $pinegrow_auto_synced_patterns_plugin_base_path_value;
    if(empty($pinegrow_auto_synced_patterns_plugin_base_path_value)) {
        $pinegrow_auto_synced_patterns_plugin_base_path_value = untrailingslashit( plugin_dir_path(  __FILE__ ) );
    }
    return $pinegrow_auto_synced_patterns_plugin_base_path_value;
}

endif;
 
if ( ! function_exists( 'pinegrow_auto_synced_patterns_setup' ) ) :

function pinegrow_auto_synced_patterns_setup() {

    pinegrow_auto_synced_patterns_plugin_base_url();
    /*
     * Make the plugin available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    /* Pinegrow generated Load Text Domain Begin */
    load_plugin_textdomain( 'pinegrow_auto_synced_patterns', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    /* Pinegrow generated Load Text Domain End */

    /*
     * Register custom menu locations
     */
    /* Pinegrow generated Register Menus Begin */

    /* Pinegrow generated Register Menus End */
    
    /*
    * Set image sizes
     */
    /* Pinegrow generated Image sizes Begin */

    /* Pinegrow generated Image sizes End */
    
}
endif; // pinegrow_auto_synced_patterns_setup

add_action( 'after_setup_theme', 'pinegrow_auto_synced_patterns_setup' );


if ( ! function_exists( 'pinegrow_auto_synced_patterns_init' ) ) :

function pinegrow_auto_synced_patterns_init() {

    /*
     * Register custom post types. You can also move this code to a plugin.
     */
    /* Pinegrow generated Custom Post Types Begin */

    /* Pinegrow generated Custom Post Types End */
    
    /*
     * Register custom taxonomies. You can also move this code to a plugin.
     */
    /* Pinegrow generated Taxonomies Begin */

    /* Pinegrow generated Taxonomies End */

}
endif; // pinegrow_auto_synced_patterns_setup

add_action( 'init', 'pinegrow_auto_synced_patterns_init' );


if ( ! function_exists( 'pinegrow_auto_synced_patterns_custom_image_sizes_names' ) ) :

function pinegrow_auto_synced_patterns_custom_image_sizes_names( $sizes ) {

    /*
     * Add names of custom image sizes.
     */
    /* Pinegrow generated Image Sizes Names Begin*/
    /* This code will be replaced by returning names of custom image sizes. */
    /* Pinegrow generated Image Sizes Names End */
    return $sizes;
}
add_action( 'image_size_names_choose', 'pinegrow_auto_synced_patterns_custom_image_sizes_names' );
endif;// pinegrow_auto_synced_patterns_custom_image_sizes_names


if ( ! function_exists( 'pinegrow_auto_synced_patterns_widgets_init' ) ) :

function pinegrow_auto_synced_patterns_widgets_init() {

    /*
     * Register widget areas.
     */
    /* Pinegrow generated Register Sidebars Begin */

    /* Pinegrow generated Register Sidebars End */
}
add_action( 'widgets_init', 'pinegrow_auto_synced_patterns_widgets_init' );
endif;// pinegrow_auto_synced_patterns_widgets_init



if ( ! function_exists( 'pinegrow_auto_synced_patterns_customize_register' ) ) :

function pinegrow_auto_synced_patterns_customize_register( $wp_customize ) {
    // Do stuff with $wp_customize, the WP_Customize_Manager object.

    /* Pinegrow generated Customizer Controls Begin */

    /* Pinegrow generated Customizer Controls End */

}
add_action( 'customize_register', 'pinegrow_auto_synced_patterns_customize_register' );
endif;// pinegrow_auto_synced_patterns_customize_register


if ( ! function_exists( 'pinegrow_auto_synced_patterns_enqueue_scripts' ) ) :
    function pinegrow_auto_synced_patterns_enqueue_scripts() {

        /* Pinegrow generated Enqueue Scripts Begin */

    /* Pinegrow generated Enqueue Scripts End */

        /* Pinegrow generated Enqueue Styles Begin */

    /* Pinegrow generated Enqueue Styles End */

    }
    add_action( 'wp_enqueue_scripts', 'pinegrow_auto_synced_patterns_enqueue_scripts' );
endif;

if ( ! function_exists( 'pinegrow_auto_synced_patterns_pgwp_sanitize_placeholder' ) ) :
    function pinegrow_auto_synced_patterns_pgwp_sanitize_placeholder($input) { return $input; }
endif;

    /*
     * Resource files included by Pinegrow.
     */
    /* Pinegrow generated Include Resources Begin */
require_once "inc/custom.php";
if( !class_exists( 'PG_Helper_v2' ) ) { require_once "inc/wp_pg_helpers.php"; }

    /* Pinegrow generated Include Resources End */
?><?php
endif; //end if ( $pinegrow_run_plugin )
