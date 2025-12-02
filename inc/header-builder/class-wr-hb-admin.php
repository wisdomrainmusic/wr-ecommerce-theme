<?php
/**
 * WR Header Builder Admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WR_HB_Admin {
    /**
     * Menu slug.
     *
     * @var string
     */
    protected $slug = 'wr-header-builder';

    /**
     * Singleton instance.
     *
     * @var WR_HB_Admin|null
     */
    protected static $instance = null;

    /**
     * Manager instance.
     *
     * @var WR_HB_Manager
     */
    protected $manager;

    /**
     * Constructor.
     */
    protected function __construct() {
        $this->manager = WR_HB_Manager::get_instance();
    }

    /**
     * Singleton accessor.
     */
    public static function get_instance(): WR_HB_Admin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize hooks.
     */
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Register admin page.
     */
    public function register_menu(): void {
        add_menu_page(
            __( 'WR Header Builder', 'wr-ecommerce-theme' ),
            __( 'Header Builder', 'wr-ecommerce-theme' ),
            'manage_options',
            $this->slug,
            [ $this, 'render_page' ],
            'dashicons-editor-table',
            59
        );
    }

    /**
     * Enqueue assets for builder page.
     */
    public function enqueue_assets( string $hook ): void {
        if ( 'toplevel_page_' . $this->slug !== $hook ) {
            return;
        }

        $nonce            = wp_create_nonce( 'wr_hb_nonce' );
        $active_layout_id = $this->manager->get_active_layout_id();
        $layout           = $this->manager->get_layout( $active_layout_id );

        wp_enqueue_style(
            'wr-hb-admin',
            get_theme_file_uri( '/assets/css/wr-hb-admin.css' ),
            [],
            $this->manager->get_version()
        );

        wp_enqueue_script(
            'sortablejs',
            get_theme_file_uri( '/assets/header-builder/js/sortable.min.js' ),
            [],
            '1.15.0',
            true
        );

        wp_enqueue_script(
            'wr-hb-admin',
            get_theme_file_uri( '/assets/js/wr-hb-admin.js' ),
            [ 'jquery', 'sortablejs' ],
            $this->manager->get_version(),
            true
        );

        wp_localize_script(
            'wr-hb-admin',
            'wrHbAdminData',
            [
                'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
                'nonce'        => $nonce,
                'layoutId'     => $active_layout_id,
                'layout'       => $layout,
            ]
        );
    }

    /**
     * Render admin page.
     */
    public function render_page(): void {
        $nonce            = wp_create_nonce( 'wr_hb_nonce' );
        $active_layout_id = $this->manager->get_active_layout_id();
        $layout           = $this->manager->get_layout( $active_layout_id );

        $widgets   = $this->get_available_widgets();
        $view_path = get_theme_file_path( 'inc/header-builder/views/admin-page-header-builder.php' );

        if ( file_exists( $view_path ) ) {
            include $view_path;
        }
    }

    /**
     * Widget list.
     */
    protected function get_available_widgets(): array {
        return [
            'logo'           => [
                'label'            => __( 'Logo', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'primary-menu'   => [
                'label'            => __( 'Primary Menu', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'secondary-menu' => [
                'label'            => __( 'Secondary Menu', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'search'         => [
                'label'            => __( 'Search', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'cart'           => [
                'label'            => __( 'Cart', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'button'         => [
                'label'            => __( 'Button', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'html'           => [
                'label'            => __( 'HTML', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
            'shortcode'      => [
                'label'            => __( 'Shortcode', 'wr-ecommerce-theme' ),
                'default_settings' => [],
            ],
        ];
    }
}
