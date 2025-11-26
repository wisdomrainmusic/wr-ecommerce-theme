<?php
/**
 * Elementor Base Compatibility for WR Theme
 * @package WR_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Elementor'un WR Theme ile uyumluluğunu bildirme
 */
function wr_elementor_compatible_theme() {
    add_theme_support( 'elementor' );
    add_theme_support( 'elementor-pro' );
}
add_action( 'after_setup_theme', 'wr_elementor_compatible_theme' );


/**
 * 2. Elementor Breakpoints (Responsive ayarları)
 */
function wr_elementor_responsive_settings() {
    if ( did_action( 'elementor/loaded' ) ) {
        \Elementor\Core\Settings\Manager::get_settings_managers( 'page' )
            ->get_model()
            ->update_settings( array(
                'elementor_container_width' => 1140,
                'elementor_space_between_widgets' => 20,
            ) );
    }
}
add_action( 'init', 'wr_elementor_responsive_settings' );


/**
 * 3. Elementor Canvas uyumluluğu
 */
add_theme_support( 'elementor-canvas' );

