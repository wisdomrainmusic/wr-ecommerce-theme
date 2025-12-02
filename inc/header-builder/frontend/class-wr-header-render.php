<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WR_Header_Builder_Render {

    public function __construct() {
        add_action( 'wr_theme_render_header', [ $this, 'render_header' ] );
    }

    public function render_header() {
        $header_id = get_theme_mod( 'wr_active_header' );
        $json = get_post_meta( $header_id, '_wr_header_json', true );

        if ( empty( $json ) ) return;

        $layout = json_decode( $json, true );

        echo '<header class="wr-header">';

        foreach ( $layout as $row ) {
            echo '<div class="wr-header-row">';

            foreach ( ['left','center','right'] as $area ) {
                echo '<div class="wr-header-col wr-col-' . $area . '">';

                if ( ! empty( $row[$area] ) ) {
                    foreach ( $row[$area] as $widget ) {
                        $this->render_widget( $widget );
                    }
                }

                echo '</div>';
            }
            echo '</div>';
        }

        echo '</header>';
    }

    private function render_widget( $widget ) {
        $type = $widget['type'] ?? '';
        $settings = $widget['settings'] ?? [];

        switch ( $type ) {
            case 'logo':
                echo '<div class="wr-hb-logo"><a href="' . home_url() . '">' . get_bloginfo('name') . '</a></div>';
                break;

            case 'menu':
                wp_nav_menu([ 'theme_location' => 'primary', 'container' => false ]);
                break;

            case 'search':
                get_search_form();
                break;

            case 'cart':
                echo '<div class="wr-hb-cart">' . do_shortcode('[woocommerce_cart]') . '</div>';
                break;

            // custom widget types buraya eklenir

        }
    }
}
