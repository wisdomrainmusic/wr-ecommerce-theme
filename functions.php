<?php
/**
 * WR E-Commerce Theme Core Functions
 * @package WR_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * -------------------------------------------------------
 * 1. THEME SETUP
 * -------------------------------------------------------
 */
function wr_theme_setup() {

    // Make theme translatable
    load_theme_textdomain( 'wr-theme', get_template_directory() . '/languages' );

    // Add menu support
    add_theme_support( 'menus' );
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'wr-theme' ),
        'footer'  => __( 'Footer Menu', 'wr-theme' ),
    ) );

    // Add widget support
    add_theme_support( 'widgets' );

    // Add featured images
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'wr-thumb-small', 150, 150, true );
    add_image_size( 'wr-thumb-medium', 400, 400, true );
    add_image_size( 'wr-thumb-large', 800, 800, true );

    // WooCommerce support
    add_theme_support( 'woocommerce' );

}
add_action( 'after_setup_theme', 'wr_theme_setup' );


/**
 * -------------------------------------------------------
 * 2. CSS & JS ENQUEUE
 * -------------------------------------------------------
 */
function wr_theme_assets() {

    // CSS
    wp_enqueue_style(
        'wr-theme-style',
        get_template_directory_uri() . '/assets/css/style.css',
        array(),
        '1.0'
    );

    // JS
    wp_enqueue_script(
        'wr-theme-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wr_theme_assets' );


/**
 * -------------------------------------------------------
 * 3. ELEMENTOR BASE COMPATIBILITY
 * -------------------------------------------------------
 */
function wr_elementor_support() {
    add_theme_support( 'elementor' );
    add_theme_support( 'elementor-pro' );
    add_theme_support( 'e-commerce' ); // future-proof
}
add_action( 'after_setup_theme', 'wr_elementor_support' );


/**
 * -------------------------------------------------------
 * 4. WR THEME PANEL LOADER (boş yapı)
 * -------------------------------------------------------
 */
function wr_theme_panel_loader() {

    $admin_panel = get_template_directory() . '/inc/admin/panel.php';

    if ( file_exists( $admin_panel ) ) {
        require_once $admin_panel;
    }

}
add_action( 'init', 'wr_theme_panel_loader' );


// -------------------------------------------------------
// 5. WR THEME PANEL ADMIN MENU
// -------------------------------------------------------
function wr_theme_panel_admin_menu() {
    add_menu_page(
        __( 'WR Theme Panel', 'wr-theme' ),
        __( 'WR Theme Panel', 'wr-theme' ),
        'manage_options',
        'wr-theme-panel',
        'wr_theme_panel_page',
        'dashicons-admin-generic',
        58
    );
}
add_action( 'admin_menu', 'wr_theme_panel_admin_menu' );


// -------------------------------------------------------
// 6. ELEMENTOR INIT LOADER
// -------------------------------------------------------
function wr_elementor_loader() {
    $file = get_template_directory() . '/inc/elementor/elementor-init.php';
    if ( file_exists( $file ) ) {
        require_once $file;
    }
}
add_action( 'after_setup_theme', 'wr_elementor_loader' );

