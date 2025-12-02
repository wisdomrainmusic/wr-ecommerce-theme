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

        wp_enqueue_style(
            'wr-hb-admin',
            get_theme_file_uri( '/assets/header-builder/css/builder-admin.css' ),
            [],
            $this->manager->get_version()
        );

        wp_enqueue_script(
            'sortablejs',
            'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js',
            [],
            '1.15.0',
            true
        );

        wp_enqueue_script(
            'wr-hb-admin',
            get_theme_file_uri( '/assets/header-builder/js/builder-admin.js' ),
            [ 'jquery', 'sortablejs' ],
            $this->manager->get_version(),
            true
        );

        wp_localize_script(
            'wr-hb-admin',
            'wrHbData',
            [
                'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
                'nonce'        => wp_create_nonce( 'wr_hb_nonce' ),
                'layouts'      => $this->manager->get_layouts(),
                'activeLayout' => $this->manager->get_active_layout_id(),
                'i18n'         => [
                    'saveSuccess' => __( 'Header layout saved.', 'wr-ecommerce-theme' ),
                    'saveError'   => __( 'Unable to save layout. Please try again.', 'wr-ecommerce-theme' ),
                ],
            ]
        );
    }

    /**
     * Render admin page.
     */
    public function render_page(): void {
        $nonce            = wp_create_nonce( 'wr_hb_nonce' );
        $active_layout_id = $this->manager->get_active_layout_id();
        ?>
        <div class="wrap wr-hb-admin">
            <h1><?php esc_html_e( 'WR Header Builder', 'wr-ecommerce-theme' ); ?></h1>
            <input type="hidden" id="wr-hb-nonce" value="<?php echo esc_attr( $nonce ); ?>" />
            <div id="wr-hb-app" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-active-layout="<?php echo esc_attr( $active_layout_id ); ?>">
                <div class="wr-hb-panel">
                    <div class="wr-hb-toolbar">
                        <div class="wr-hb-device-switch">
                            <button type="button" class="button button-secondary active" data-device="desktop"><?php esc_html_e( 'Desktop', 'wr-ecommerce-theme' ); ?></button>
                            <button type="button" class="button button-secondary" data-device="tablet"><?php esc_html_e( 'Tablet', 'wr-ecommerce-theme' ); ?></button>
                            <button type="button" class="button button-secondary" data-device="mobile"><?php esc_html_e( 'Mobile', 'wr-ecommerce-theme' ); ?></button>
                        </div>
                        <div class="wr-hb-actions">
                            <button type="button" class="button" id="wr-hb-add-row"><?php esc_html_e( 'Add Row', 'wr-ecommerce-theme' ); ?></button>
                            <button type="button" class="button button-primary" id="wr-hb-save-layout"><?php esc_html_e( 'Save Layout', 'wr-ecommerce-theme' ); ?></button>
                        </div>
                    </div>

                    <div class="wr-hb-builder">
                        <div class="wr-hb-canvas" id="wr-hb-canvas"></div>
                        <div class="wr-hb-sidebar">
                            <h3><?php esc_html_e( 'Widgets', 'wr-ecommerce-theme' ); ?></h3>
                            <p class="description"><?php esc_html_e( 'Drag items into columns', 'wr-ecommerce-theme' ); ?></p>
                            <div id="wr-hb-widget-library" class="wr-hb-widget-library">
                                <?php foreach ( $this->get_available_widgets() as $type => $label ) : ?>
                                    <div class="wr-hb-widget" data-type="<?php echo esc_attr( $type ); ?>">
                                        <span class="wr-hb-widget-label"><?php echo esc_html( $label ); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="wr-hb-help">
                                <p><?php esc_html_e( 'Tip: You can reorder rows, columns and widgets freely. Columns support width selection (25% - 100%).', 'wr-ecommerce-theme' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Widget list.
     */
    protected function get_available_widgets(): array {
        return [
            'logo'   => __( 'Logo', 'wr-ecommerce-theme' ),
            'menu'   => __( 'Primary Menu', 'wr-ecommerce-theme' ),
            'search' => __( 'Search', 'wr-ecommerce-theme' ),
            'cart'   => __( 'Cart', 'wr-ecommerce-theme' ),
            'button' => __( 'Button', 'wr-ecommerce-theme' ),
            'html'   => __( 'HTML Block', 'wr-ecommerce-theme' ),
            'spacer' => __( 'Spacer', 'wr-ecommerce-theme' ),
        ];
    }
}
