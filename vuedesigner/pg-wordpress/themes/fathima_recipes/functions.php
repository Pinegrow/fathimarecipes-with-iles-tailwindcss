<?php
if ( ! function_exists( 'fathima_recipes_setup' ) ) :

function fathima_recipes_setup() {

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    /* Pinegrow generated Load Text Domain Begin */
    load_theme_textdomain( 'fathima_recipes', get_template_directory() . '/languages' );
    /* Pinegrow generated Load Text Domain End */

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     */
    add_theme_support( 'title-tag' );
    
    /*
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support( 'post-thumbnails' );
    //Support custom logo
    add_theme_support( 'custom-logo' );

    // Add menus.
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'fathima_recipes' ),
        'social'  => __( 'Social Links Menu', 'fathima_recipes' ),
    ) );

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
    
    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );

    /*
     * Enable support for Post Formats.
     */
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
    ) );

    /*
     * Enable support for Page excerpts.
     */
     add_post_type_support( 'page', 'excerpt' );
}
endif; // fathima_recipes_setup

add_action( 'after_setup_theme', 'fathima_recipes_setup' );


if ( ! function_exists( 'fathima_recipes_init' ) ) :

function fathima_recipes_init() {

    
    // Use categories and tags with attachments
    register_taxonomy_for_object_type( 'category', 'attachment' );
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );

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
endif; // fathima_recipes_setup

add_action( 'init', 'fathima_recipes_init' );


if ( ! function_exists( 'fathima_recipes_custom_image_sizes_names' ) ) :

function fathima_recipes_custom_image_sizes_names( $sizes ) {

    /*
     * Add names of custom image sizes.
     */
    /* Pinegrow generated Image Sizes Names Begin*/
    /* This code will be replaced by returning names of custom image sizes. */
    /* Pinegrow generated Image Sizes Names End */
    return $sizes;
}
add_action( 'image_size_names_choose', 'fathima_recipes_custom_image_sizes_names' );
endif;// fathima_recipes_custom_image_sizes_names



if ( ! function_exists( 'fathima_recipes_widgets_init' ) ) :

function fathima_recipes_widgets_init() {

    /*
     * Register widget areas.
     */
    /* Pinegrow generated Register Sidebars Begin */

    /* Pinegrow generated Register Sidebars End */
}
add_action( 'widgets_init', 'fathima_recipes_widgets_init' );
endif;// fathima_recipes_widgets_init



if ( ! function_exists( 'fathima_recipes_customize_register' ) ) :

function fathima_recipes_customize_register( $wp_customize ) {
    // Do stuff with $wp_customize, the WP_Customize_Manager object.

    /* Pinegrow generated Customizer Controls Begin */

    /* Pinegrow generated Customizer Controls End */

}
add_action( 'customize_register', 'fathima_recipes_customize_register' );
endif;// fathima_recipes_customize_register


if ( ! function_exists( 'fathima_recipes_enqueue_scripts' ) ) :
    function fathima_recipes_enqueue_scripts() {

        /* Pinegrow generated Enqueue Scripts Begin */

    /* Pinegrow generated Enqueue Scripts End */

        /* Pinegrow generated Enqueue Styles Begin */

    wp_deregister_style( 'fathima_recipes-tailwind' );
    wp_enqueue_style( 'fathima_recipes-tailwind', get_template_directory_uri() . '/tailwind_theme/tailwind.css', [], '1.0.17', 'all');

    wp_deregister_style( 'fathima_recipes-style' );
    wp_enqueue_style( 'fathima_recipes-style', get_bloginfo('stylesheet_url'), [], '1.0.17', 'all');

    /* Pinegrow generated Enqueue Styles End */

    }
    add_action( 'wp_enqueue_scripts', 'fathima_recipes_enqueue_scripts' );
endif;

function pgwp_sanitize_placeholder($input) { return $input; }
/*
 * Resource files included by Pinegrow.
 */
/* Pinegrow generated Include Resources Begin */
require_once "inc/custom.php";
if( !class_exists( 'PG_Helper_v2' ) ) { require_once "inc/wp_pg_helpers.php"; }
if( !class_exists( 'PG_Blocks_v4' ) ) { require_once "inc/wp_pg_blocks_helpers.php"; }

    /* Pinegrow generated Include Resources End */

/* Creating Editor Blocks with Pinegrow */

if ( ! function_exists('fathima_recipes_blocks_init') ) :
function fathima_recipes_blocks_init() {
    // Register blocks. Don't edit anything between the following comments.
    /* Pinegrow generated Register Pinegrow Blocks Begin */
    require_once 'blocks/hero-seasonal/hero-seasonal_register.php';

    /* Pinegrow generated Register Pinegrow Blocks End */
}
add_action('init', 'fathima_recipes_blocks_init');
endif;

/* End of creating Editor Blocks with Pinegrow */


/* Setting up theme supports options */

function fathima_recipes_setup_theme_supports() {
    // Don't edit anything between the following comments.
    /* Pinegrow generated Theme Supports Begin */
    
//Enable site editor                    
add_theme_support('block-templates');
add_theme_support('block-template-parts');    
//Tell WP to scope loaded editor styles to the block editor                    
add_theme_support( 'editor-styles' );
    /* Pinegrow generated Theme Supports End */
}
add_action('after_setup_theme', 'fathima_recipes_setup_theme_supports');

/* End of setting up theme supports options */


/* Loading editor styles for blocks */

function fathima_recipes_add_blocks_editor_styles() {
// Add blocks editor styles. Don't edit anything between the following comments.
/* Pinegrow generated Load Blocks Editor Styles Begin */
    add_editor_style( 'tailwind_theme/tailwind_for_wp_editor.css' );
    add_editor_style( 'additional-editor-styles.css' );

    /* Pinegrow generated Load Blocks Editor Styles End */
}
add_action('admin_init', 'fathima_recipes_add_blocks_editor_styles');

/* End of loading editor styles for blocks */


/* Register Blocks Categories */

function fathima_recipes_register_blocks_categories( $categories ) {

    // Don't edit anything between the following comments.
    /* Pinegrow generated Register Blocks Category Begin */

$categories = array_merge( $categories, array( array(
        'slug' => 'my_blocks',
        'title' => __( 'My blocks', 'fathima_recipes' )
    ) ) );

    /* Pinegrow generated Register Blocks Category End */
    
    return $categories;
}
add_action( version_compare('5.8', get_bloginfo('version'), '<=' ) ? 'block_categories_all' : 'block_categories', 'fathima_recipes_register_blocks_categories');

/* End of registering Blocks Categories */

?>