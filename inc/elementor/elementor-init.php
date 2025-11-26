<?php
/**
 * WR Elementor Compatibility Init (SAFE VERSION)
 * Compatible with Elementor 3.20+
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load Elementor safely.
 */
function wr_elementor_init() {

    // Elementor yoksa devam etme
    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }

    // Breakpoints veya özel ayar EKSİK İSE model kullanmıyoruz
    // Çünkü update_settings() artık kullanılmıyor.

    // Sadece log basalım (debug)
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'WR Elementor Init Loaded (SAFE VERSION)' );
    }
}
add_action( 'init', 'wr_elementor_init' );
