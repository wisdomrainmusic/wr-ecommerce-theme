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
            get_theme_file_uri( '/assets/header-builder/css/builder-frontend.css' ),
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
        $active_layout_id = $this->manager->get_active_layout_id();
        $layout           = $this->manager->get_layout( $active_layout_id ?? 'layout-desktop' );
        $device           = $layout['device'] ?? 'desktop';

        if ( empty( $layout['rows'] ) || ! is_array( $layout['rows'] ) ) {
            return;
        }

        echo '<header class="wr-hb wr-hb-device-' . esc_attr( $device ) . '"><div class="wr-hb-container">';

        foreach ( $layout['rows'] as $row ) {
            echo '<div class="wr-hb-row" data-row-id="' . esc_attr( $row['id'] ) . '">';
            echo '<div class="wr-hb-row-inner">';

            if ( ! empty( $row['columns'] ) ) {
                usort(
                    $row['columns'],
                    static function ( $a, $b ) {
                        return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
                    }
                );

                foreach ( $row['columns'] as $column ) {
                    $width = isset( $column['width'] ) ? $column['width'] : '33%';
                    echo '<div class="wr-hb-column" style="width:' . esc_attr( $width ) . '" data-column-id="' . esc_attr( $column['id'] ) . '">';
                    echo '<div class="wr-hb-widget-stack">';

                    if ( ! empty( $column['widgets'] ) ) {
                        usort(
                            $column['widgets'],
                            static function ( $a, $b ) {
                                return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
                            }
                        );

                        foreach ( $column['widgets'] as $widget ) {
                            $this->render_widget( $widget );
                        }
                    }

                    echo '</div>';
                    echo '</div>';
                }
            }

            echo '</div>';
            echo '</div>';
        }

        echo '</div></header>';
    }

    /**
     * Render individual widget types.
     */
    protected function render_widget( array $widget ): void {
        $type     = $widget['type'] ?? 'html';
        $settings = $widget['settings'] ?? [];

        ob_start();

        echo '<div class="wr-hb-widget-item wr-hb-widget-' . esc_attr( $type ) . '" data-widget-id="' . esc_attr( $widget['id'] ?? '' ) . '">';

        switch ( $type ) {
            case 'logo':
                echo '<div class="wr-hb-logo">';
                if ( has_custom_logo() ) {
                    the_custom_logo();
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
                get_search_form();
                break;

            case 'cart':
                if ( function_exists( 'wr_get_mini_cart' ) ) {
                    wr_get_mini_cart();
                } elseif ( function_exists( 'woocommerce_mini_cart' ) ) {
                    woocommerce_mini_cart();
                }
                break;

            case 'button':
                $label = $settings['label'] ?? __( 'Button', 'wr-ecommerce-theme' );
                $url   = $settings['url'] ?? home_url( '/' );
                echo '<a class="wr-hb-button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
                break;

            case 'spacer':
                $height = $settings['height'] ?? '20px';
                echo '<div class="wr-hb-spacer" style="height:' . esc_attr( $height ) . ';"></div>';
                break;

            case 'shortcode':
                $shortcode = $settings['shortcode'] ?? '';
                echo '<div class="wr-hb-shortcode">' . do_shortcode( $shortcode ) . '</div>';
                break;

            case 'html':
            default:
                $content = $settings['content'] ?? __( 'Custom HTML', 'wr-ecommerce-theme' );
                echo '<div class="wr-hb-html">' . wp_kses_post( $content ) . '</div>';
                break;
        }

        $html = ob_get_clean();

        echo apply_filters( 'wr_hb_render_widget', $html, $widget );
    }
}
