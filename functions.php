<?php
/**
 * WR E-Commerce Theme Core Functions
 * @package WR_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------
// WooCommerce Blocks devre dışı (çift buton/quick view fix)
// -----------------------------
add_filter( 'woocommerce_blocks_use_blockified_product_grid', '__return_false' );

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
    add_theme_support( 'e-commerce' );
}
add_action( 'after_setup_theme', 'wr_elementor_support' );


/**
 * -------------------------------------------------------
 * 4. WR THEME PANEL LOADER
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


// -------------------------------------------------------
// 7. WR THEME PANEL ADMIN SCRIPTS & STYLES
// -------------------------------------------------------
function wr_theme_admin_assets($hook) {

    if ( $hook !== 'toplevel_page_wr-theme-panel' ) {
        return;
    }

    wp_enqueue_style(
        'wr-theme-admin',
        get_template_directory_uri() . '/assets/admin/css/admin.css',
        array(),
        '1.0'
    );

    wp_enqueue_script(
        'wr-theme-admin',
        get_template_directory_uri() . '/assets/admin/js/admin.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
add_action( 'admin_enqueue_scripts', 'wr_theme_admin_assets' );


/* -------------------------------------------------------
 * 8. WR SHOP RENDERER + Shop CSS/JS
 * ------------------------------------------------------*/
function wr_shop_loader() {

    $renderer = get_template_directory() . '/inc/woocommerce/shop-render.php';
    if ( file_exists( $renderer ) ) {
        require_once $renderer;
    }

    wp_enqueue_style(
        'wr-shop-style',
        get_template_directory_uri() . '/assets/css/shop.css',
        array(),
        '1.0'
    );

    wp_enqueue_script(
        'wr-shop-js',
        get_template_directory_uri() . '/assets/js/shop.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wr_shop_loader' );


/* -------------------------------------------------------
 * 9. WR SINGLE PRODUCT LOADER
 * ------------------------------------------------------*/
function wr_single_product_loader() {

    $file = get_template_directory() . '/inc/woocommerce/single-render.php';
    if ( file_exists( $file ) ) {
        require_once $file;
    }

    wp_enqueue_style(
        'wr-single-style',
        get_template_directory_uri() . '/assets/css/single.css',
        array(),
        '1.0'
    );

    wp_enqueue_script(
        'wr-single-js',
        get_template_directory_uri() . '/assets/js/single.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wr_single_product_loader' );


/* -------------------------------------------------------
 * 10. WR MINI CART SYSTEM LOADER
 * ------------------------------------------------------*/
function wr_mini_cart_system_loader() {

    $file = get_template_directory() . '/inc/woocommerce/mini-cart-render.php';
    if ( file_exists( $file ) ) {
        require_once $file;
    }

    wp_enqueue_style(
        'wr-mini-cart-style',
        get_template_directory_uri() . '/assets/css/mini-cart.css',
        array(),
        '1.0'
    );

    wp_enqueue_script(
        'wr-mini-cart-js',
        get_template_directory_uri() . '/assets/js/mini-cart.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wr_mini_cart_system_loader' );

require_once get_template_directory() . '/inc/woocommerce/mini-cart.php';

add_filter( 'woocommerce_add_to_cart_fragments', 'wr_ajax_cart_fragments' );

function wr_ajax_cart_fragments( $fragments ) {
    ob_start();
    ?>
    <span class="wr-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.wr-cart-count'] = ob_get_clean();
    return $fragments;
}


/* -------------------------------------------------------
 * 11. WR QUICK VIEW LOADER
 * ------------------------------------------------------*/
function wr_quick_view_loader() {

    $file = get_template_directory() . '/inc/woocommerce/quick-view.php';

    if ( file_exists( $file ) ) {
        require_once $file;
    }
}
add_action( 'after_setup_theme', 'wr_quick_view_loader' );


/* -------------------------------------------------------
 * 12. WR PRODUCT CARD STYLES
 * ------------------------------------------------------*/
function wr_product_card_styles_loader() {

    if ( is_admin() ) {
        return;
    }

    wp_enqueue_style(
        'wr-product-card',
        get_template_directory_uri() . '/assets/css/product-card.css',
        array(),
        '1.1'
    );
}
add_action( 'wp_enqueue_scripts', 'wr_product_card_styles_loader', 20 );


/* -------------------------------------------------------
 * 13. OVERRIDE – WooCommerce Product Loop → WR CARD
 * ------------------------------------------------------*/
add_filter( 'wc_get_template', 'wr_override_product_loop_template', 10, 5 );

function wr_override_product_loop_template( $located, $template_name, $args, $template_path, $default_path ) {

    $target = array(
        'content-product.php',
        'loop/content-product.php'
    );

    if ( in_array( $template_name, $target, true ) ) {

        $custom = get_template_directory() . '/woocommerce/loop/product-card.php';

        if ( file_exists( $custom ) ) {
            return $custom;
        }
    }

    return $located;
}

// ==================================================
// WR HEADER BUILDER LOADER
// ==================================================
require_once get_theme_file_path( '/inc/header-builder/class-wr-header-builder.php' );
require_once get_theme_file_path( '/inc/header-builder/admin/class-wr-header-admin.php' );
require_once get_theme_file_path( '/inc/header-builder/frontend/class-wr-header-render.php' );

new WR_Header_Builder();
