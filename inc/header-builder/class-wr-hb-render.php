<?php
/**
 * WR Header Builder Renderer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WR_HB_Render {
    /**
     * Manager instance.
     *
     * @var WR_HB_Manager
     */
    protected $manager;

    /**
     * Singleton instance.
     *
     * @var WR_HB_Render|null
     */
    protected static $instance = null;

    /**
     * Constructor.
     */
    protected function __construct() {
        $this->manager = WR_HB_Manager::get_instance();
    }

    /**
     * Singleton accessor.
     */
    public static function get_instance(): WR_HB_Render {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Bootstrap hooks.
     */
    public function init(): void {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );

        if ( has_action( 'wr_theme_render_header' ) ) {
            add_action( 'wr_theme_render_header', [ $this, 'output_header' ] );
        } else {
            // Fallback to wp_body_open when theme-specific header hook is absent.
            add_action( 'wp_body_open', [ $this, 'output_header' ] );
        }
    }

    /**
     * Enqueue frontend assets.
     */
    public function enqueue(): void {
        wp_enqueue_style(
            'wr-hb-frontend',
            get_theme_file_uri( '/assets/css/wr-hb-frontend.css' ),
            [],
            $this->manager->get_version()
        );

        wp_enqueue_script(
            'wr-hb-frontend',
            get_theme_file_uri( '/assets/header-builder/js/builder-frontend.js' ),
            [ 'jquery' ],
            $this->manager->get_version(),
            true
        );
    }

    /**
     * Render header layout.
     */
    public function output_header(): void {
        $layout_id = $this->manager->get_active_layout_id();

        if ( ! $layout_id ) {
            return;
        }

        $layout = $this->manager->get_layout( $layout_id );

        if ( empty( $layout['rows'] ) || ! is_array( $layout['rows'] ) ) {
            return;
        }

        $device = $layout['device'] ?? 'desktop';

        echo '<header class="wr-hb-header">';

        $rows = $layout['rows'];

        usort(
            $rows,
            static function ( $a, $b ) {
                return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
            }
        );

        foreach ( $rows as $row ) {
            $row_id = $row['id'] ?? '';

            echo '<div class="wr-hb-row" data-row-id="' . esc_attr( $row_id ) . '">';

            $columns = is_array( $row['columns'] ?? null ) ? $row['columns'] : [];

            usort(
                $columns,
                static function ( $a, $b ) {
                    return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
                }
            );

            foreach ( $columns as $column ) {
                $column_id = $column['id'] ?? '';
                $col_width = $column['width'] ?? '';
                $col_device = $column['device'] ?? $device;
                $style      = $col_width ? ' style="width:' . esc_attr( $col_width ) . '"' : '';

                echo '<div class="wr-hb-col wr-hb-col--' . esc_attr( $col_device ) . '" data-column-id="' . esc_attr( $column_id ) . '"' . $style . '>';

                $widgets = is_array( $column['widgets'] ?? null ) ? $column['widgets'] : [];

                usort(
                    $widgets,
                    static function ( $a, $b ) {
                        return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
                    }
                );

                foreach ( $widgets as $widget ) {
                    $this->render_widget( $widget );
                }

                echo '</div>';
            }

            echo '</div>';
        }

        echo '</header>';
    }

    /**
     * Render individual widget types.
     */
    protected function render_widget( array $widget ): void {
        $type        = $widget['type'] ?? 'html';
        $settings    = $widget['settings'] ?? [];
        $widget_id   = $widget['id'] ?? '';
        $button_url  = isset( $settings['url'] ) ? esc_url( $settings['url'] ) : esc_url( home_url( '/' ) );
        $button_text = $settings['label'] ?? __( 'Button', 'wr-ecommerce-theme' );

        ob_start();

        echo '<div class="wr-hb-widget wr-hb-widget--' . esc_attr( $type ) . '" data-widget-id="' . esc_attr( $widget_id ) . '">';

        switch ( $type ) {
            case 'logo':
                echo '<div class="wr-hb-widget__logo">';

                if ( has_custom_logo() ) {
                    echo wp_kses_post( get_custom_logo() );
                } else {
                    echo '<a class="wr-hb-logo__text" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
                }

                echo '</div>';
                break;

            case 'menu':
                wp_nav_menu(
                    [
                        'theme_location' => 'primary',
                        'container'      => 'nav',
                        'menu_class'     => 'wr-hb-menu',
                        'fallback_cb'    => '__return_empty_string',
                    ]
                );
                break;

            case 'search':
                echo '<div class="wr-hb-search">' . get_search_form( false ) . '</div>';
                break;

            case 'cart':
                if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart ) {
                    $count = (int) WC()->cart->get_cart_contents_count();
                    echo '<a class="wr-hb-cart" href="' . esc_url( wc_get_cart_url() ) . '" aria-label="' . esc_attr__( 'View cart', 'wr-ecommerce-theme' ) . '">';
                    echo '<span class="wr-hb-cart__icon" aria-hidden="true">🛒</span>';
                    echo '<span class="wr-hb-cart__count">' . esc_html( $count ) . '</span>';
                    echo '</a>';
                }
                break;

            case 'button':
                echo '<a class="wr-hb-btn" href="' . $button_url . '">' . esc_html( $button_text ) . '</a>';
                break;

            case 'shortcode':
                $shortcode = $settings['shortcode'] ?? '';
                echo '<div class="wr-hb-shortcode">' . do_shortcode( $shortcode ) . '</div>';
                break;

            case 'html':
            default:
                $content = $settings['content'] ?? '';
                echo '<div class="wr-hb-html">' . wp_kses_post( $content ) . '</div>';
                break;
        }

        $html = ob_get_clean();

        echo apply_filters( 'wr_hb_render_widget', $html, $widget );
    }
}
