<?php
/**
 * Product loop content template
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WR_Ecommerce_Theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Visibility check.
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<li <?php wc_product_class( 'wr-product-card', $product ); ?>>

    <div class="wr-product-inner">

        <a href="<?php the_permalink(); ?>" class="wr-product-thumb-wrap">
            <div class="wr-product-thumb-ratio">
                <?php
                /**
                 * Product image (keeps Woo hooks inside)
                 */
                do_action( 'woocommerce_before_shop_loop_item_title' );
                ?>
            </div>
        </a>

        <div class="wr-product-content">
            <?php
            /**
             * Product title
             */
            echo '<h3 class="wr-product-title"><a href="' . esc_url( get_the_permalink() ) . '">';
            do_action( 'woocommerce_shop_loop_item_title' );
            echo '</a></h3>';

            /**
             * Price + rating
             */
            echo '<div class="wr-product-price-rating">';
                do_action( 'woocommerce_after_shop_loop_item_title' );
            echo '</div>';

            /**
             * Add to cart button
             */
            echo '<div class="wr-product-actions">';
                do_action( 'woocommerce_after_shop_loop_item' );
            echo '</div>';
            ?>
        </div>

    </div>

</li>
