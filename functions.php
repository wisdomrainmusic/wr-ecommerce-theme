<?php
/**
 * Theme setup and asset loading.
 *
 * @package WR_Ecommerce_Theme
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wr_theme_setup(): void {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'wr-ecommerce-theme' ),
        )
    );
}
add_action( 'after_setup_theme', 'wr_theme_setup' );

function wr_theme_scripts(): void {
    $theme       = wp_get_theme();
    $theme_uri   = get_template_directory_uri();
    $asset_path  = get_template_directory();
    $theme_ver   = $theme->get( 'Version' );
    $style_file  = $asset_path . '/assets/css/style.css';
    $script_file = $asset_path . '/assets/js/main.js';

    wp_enqueue_style( 'wr-theme-style', get_stylesheet_uri(), array(), $theme_ver );

    if ( file_exists( $style_file ) ) {
        wp_enqueue_style( 'wr-theme-main', $theme_uri . '/assets/css/style.css', array( 'wr-theme-style' ), (string) filemtime( $style_file ) );
    }

    if ( file_exists( $script_file ) ) {
        wp_enqueue_script( 'wr-theme-script', $theme_uri . '/assets/js/main.js', array( 'jquery' ), (string) filemtime( $script_file ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'wr_theme_scripts' );
