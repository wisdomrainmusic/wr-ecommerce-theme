<?php
/**
 * WR Mini Cart System
 * - Footer output
 * - Shortcode
 * - AJAX fragments
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ana mini cart markup
 */
function wr_mini_cart_markup() {

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return;
    }

    $cart = WC()->cart;
    ?>
    <div class="wr-mini-cart" data-wr-mini-cart>
        <div class="wr-mini-cart-header">
            <span class="wr-mini-cart-title"><?php esc_html_e( 'Your Cart', 'wr-theme' ); ?></span>
            <button type="button" class="wr-mini-cart-close" data-wr-mini-cart-close>&times;</button>
        </div>

        <div class="wr-mini-cart-body">
            <?php if ( ! $cart->is_empty() ) : ?>
                <ul class="wr-mini-cart-list">
                    <?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
                        $product   = $cart_item['data'];
                        if ( ! $product || ! $product->exists() ) {
                            continue;
                        }
                        $product_name  = $product->get_name();
                        $product_link  = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';
                        $thumbnail     = $product->get_image( 'thumbnail' );
                        $quantity      = $cart_item['quantity'];
                        $line_subtotal = $cart->get_product_subtotal( $product, $quantity );
                        ?>
                        <li class="wr-mini-cart-item">
                            <div class="wr-mini-cart-thumb">
                                <a href="<?php echo esc_url( $product_link ); ?>">
                                    <?php echo $thumbnail; ?>
                                </a>
                            </div>
                            <div class="wr-mini-cart-meta">
                                <a class="wr-mini-cart-name" href="<?php echo esc_url( $product_link ); ?>">
                                    <?php echo esc_html( $product_name ); ?>
                                </a>
                                <div class="wr-mini-cart-qty-price">
                                    <span class="wr-mini-cart-qty"><?php echo esc_html( 'x ' . $quantity ); ?></span>
                                    <span class="wr-mini-cart-price"><?php echo wp_kses_post( $line_subtotal ); ?></span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="wr-mini-cart-footer">
                    <div class="wr-mini-cart-total">
                        <span><?php esc_html_e( 'Subtotal:', 'wr-theme' ); ?></span>
                        <strong><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></strong>
                    </div>
                    <div class="wr-mini-cart-buttons">
                        <a class="button wr-mini-cart-view-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
                            <?php esc_html_e( 'View Cart', 'wr-theme' ); ?>
                        </a>
                        <a class="button wr-mini-cart-checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
                            <?php esc_html_e( 'Checkout', 'wr-theme' ); ?>
                        </a>
                    </div>
                </div>
            <?php else : ?>
                <p class="wr-mini-cart-empty">
                    <?php esc_html_e( 'Your cart is currently empty.', 'wr-theme' ); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Footer'da mini cart çıktısı
 */
function wr_output_mini_cart_footer() {
    ?>
    <div class="wr-mini-cart-toggle" data-wr-mini-cart-toggle>
        <span class="wr-mini-cart-toggle-count"><?php echo WC()->cart ? intval( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
        <span class="wr-mini-cart-toggle-label"><?php esc_html_e( 'Cart', 'wr-theme' ); ?></span>
    </div>
    <div class="wr-mini-cart-container" data-wr-mini-cart-container>
        <?php wr_mini_cart_markup(); ?>
    </div>
    <?php
}
add_action( 'wp_footer', 'wr_output_mini_cart_footer' );

/**
 * Shortcode: [wr_mini_cart]
 */
function wr_mini_cart_shortcode() {
    ob_start();
    wr_mini_cart_markup();
    return ob_get_clean();
}
add_shortcode( 'wr_mini_cart', 'wr_mini_cart_shortcode' );

/**
 * AJAX fragments: Add to cart sonrası mini cart ve badge güncelle
 */
function wr_mini_cart_fragments( $fragments ) {

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return $fragments;
    }

    ob_start();
    wr_mini_cart_markup();
    $fragments['div.wr-mini-cart'] = ob_get_clean();

    ob_start();
    ?>
    <span class="wr-mini-cart-toggle-count">
        <?php echo intval( WC()->cart->get_cart_contents_count() ); ?>
    </span>
    <?php
    $fragments['span.wr-mini-cart-toggle-count'] = ob_get_clean();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'wr_mini_cart_fragments' );
