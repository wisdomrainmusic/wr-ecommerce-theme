<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WR_Header_Builder {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );

        new WR_Header_Builder_Admin();
        new WR_Header_Builder_Render();
    }

    public function register_cpt() {
        register_post_type( 'wr_header_builder', [
            'label' => 'Header Builder',
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-editor-table',
            'supports' => ['title'],
        ]);
    }

    public function admin_assets() {
        wp_enqueue_style( 'wr-hb-admin', get_theme_file_uri( '/assets/header-builder/css/admin.css' ) );
        wp_enqueue_script( 'wr-hb-admin', get_theme_file_uri( '/assets/header-builder/js/admin.js' ), ['jquery'], false, true );
    }

    public function frontend_assets() {
        wp_enqueue_style( 'wr-hb-front', get_theme_file_uri( '/assets/header-builder/css/frontend.css' ) );
        wp_enqueue_script( 'wr-hb-front', get_theme_file_uri( '/assets/header-builder/js/frontend.js' ), ['jquery'], false, true );
    }
}
