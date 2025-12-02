<?php
/**
 * WR Header Builder – Admin bootstrap.
 *
 * @package WR_Ecommerce_Theme
 */

namespace WR_Theme\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WR_HB_Admin {

    /**
     * Singleton.
     *
     * @var WR_HB_Admin
     */
    protected static $instance;

    /**
     * Get instance.
     */
    public static function get_instance() {
        if ( null === static::$instance ) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Init hooks.
     */
    public function init() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Register admin menu page.
     */
    public function register_menu() {
        add_menu_page(
            __( 'Header Builder', 'wr-ecommerce-theme' ),
            __( 'Header Builder', 'wr-ecommerce-theme' ),
            'manage_options',
            'wr-header-builder',
            [ $this, 'render_page' ],
            'dashicons-editor-kitchensink',
            59
        );
    }

    /**
     * Enqueue scripts/styles on builder page.
     *
     * @param string $hook Current admin hook.
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_wr-header-builder' !== $hook ) {
            return;
        }
        /**
         * SortableJS (bundled or CDN fallback).
         *
         * Bazı ortamlarda eski header builder refaktör edilirken
         * sortable.min.js başka klasöre taşınmış olabilir.
         * Önce tema içi dosyayı deneriz, yoksa CDN’den yükleriz.
         */
        $sortable_src = '';
        $sortable_rel = '/assets/header-builder/js/sortable.min.js';

        if ( file_exists( get_template_directory() . $sortable_rel ) ) {
            $sortable_src = get_template_directory_uri() . $sortable_rel;
        } else {
            // Güvenli fallback – sadece admin’de kullanıyoruz.
            $sortable_src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
        }

        wp_enqueue_script(
            'sortablejs',
            $sortable_src,
            [],
            '1.15.0',
            true
        );

        // jQuery garanti olsun diye.
        wp_enqueue_script( 'jquery' );

        // Admin JS.
        wp_register_script(
            'wr-hb-admin',
            get_template_directory_uri() . '/assets/js/wr-hb-admin.js',
            [ 'jquery', 'sortablejs', 'wp-util' ],
            '1.0.0',
            true
        );

        // Admin CSS.
        wp_enqueue_style(
            'wr-hb-admin',
            get_template_directory_uri() . '/assets/css/wr-hb-admin.css',
            [],
            '1.0.0'
        );
    }

    /**
     * Render admin page.
     */
    public function render_page() {
        $view = get_template_directory() . '/inc/header-builder/views/admin-page-header-builder.php';
        if ( file_exists( $view ) ) {
            include $view;
        }
    }
}

// Maintain backwards compatibility with non-namespaced references.
class_alias( __NAMESPACE__ . '\\WR_HB_Admin', 'WR_HB_Admin' );
