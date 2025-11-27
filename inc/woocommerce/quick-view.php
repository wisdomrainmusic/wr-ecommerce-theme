<?php
/**
 * WR Quick View System
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

// Eskiden loop’tan Quick View butonu basıyordu — artık product-card.php basıyor
// add_action( 'woocommerce_after_shop_loop_item', 'wr_quick_view_button' );

class WR_Quick_View {

    public function __construct() {
        add_action( 'wp_ajax_wr_quick_view', [ $this, 'load_quick_view' ] );
        add_action( 'wp_ajax_nopriv_wr_quick_view', [ $this, 'load_quick_view' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

        add_action( 'wp_footer', [ $this, 'render_modal' ] );
        add_action( 'wr_quick_view_button', [ $this, 'render_button' ] );
    }

    /**
     * Enqueue assets and expose AJAX data.
     */
    public function register_assets() {
        $version = time();

        wp_enqueue_style(
            'wr-quick-view-style',
            get_template_directory_uri() . '/assets/css/quick-view.css',
            array(),
            $version
        );

        wp_enqueue_script(
            'wr-quick-view-js',
            get_template_directory_uri() . '/assets/js/quick-view.js',
            array( 'jquery' ),
            $version,
            true
        );

        wp_localize_script(
            'wr-quick-view-js',
            'wr_ajax',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            )
        );
    }

    /**
     * Output the quick view trigger button on product loops.
     */
    public function render_button() {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        printf(
            '<button type="button" class="button wr-quick-view-btn" data-id="%1$s" aria-label="%2$s">%3$s</button>',
            esc_attr( $product->get_id() ),
            esc_attr( sprintf( __( 'Open quick view for %s', 'wr-theme' ), $product->get_name() ) ),
            esc_html__( 'Quick View', 'wr-theme' )
        );
    }

    /**
     * AJAX: Load product markup for modal.
     */
    public function load_quick_view() {
        $product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

        if ( ! $product_id ) {
            wp_send_json_error( __( 'Missing product ID.', 'wr-theme' ), 400 );
        }

        global $product;
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            wp_send_json_error( __( 'Product not found.', 'wr-theme' ), 404 );
        }

        global $post;
        $post = get_post( $product_id );

        if ( ! $post ) {
            wp_send_json_error( __( 'Product not found.', 'wr-theme' ), 404 );
        }

        setup_postdata( $post );

        $image_url = $product->get_image_id()
            ? wp_get_attachment_image_url( $product->get_image_id(), 'large' )
            : wc_placeholder_img_src();

        ob_start();
        ?>
        <div class="wr-qv-content">
            <h2 class="wr-qv-title"><?php echo esc_html( $product->get_name() ); ?></h2>

            <?php if ( $image_url ) : ?>
                <div class="wr-qv-image">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" />
                </div>
            <?php endif; ?>

            <?php if ( $product->get_price_html() ) : ?>
                <p class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
            <?php endif; ?>

            <?php if ( $product->get_short_description() ) : ?>
                <div class="desc"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
            <?php endif; ?>

            <div class="wr-qv-actions">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();

        wp_send_json_success( ob_get_clean() );
    }

    /**
     * Render the modal container in the footer.
     */
    public function render_modal() {
        wc_get_template( 'global/quick-view.php', array(), '', get_template_directory() . '/woocommerce/' );
    }
}

new WR_Quick_View();
