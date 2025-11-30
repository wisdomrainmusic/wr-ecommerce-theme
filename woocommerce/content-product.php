<?php
/**
 * WR Theme – Unified Product Card
 * WooCommerce Loop Override + Wishlist Compatible + Single Thumbnail
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class( 'wr-product-card', $product ); ?>>

    <div class="wr-product-card__inner">

        <!-- IMAGE WRAPPER -->
        <div class="wr-product-card__thumb-wrap">

            <!-- Wishlist Button (Top Right via Hook) -->
            <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>

            <!-- THUMBNAIL -->
            <a href="<?php the_permalink(); ?>" class="wr-product-card__thumb-link">
                <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
            </a>
        </div>

        <!-- CONTENT -->
        <div class="wr-product-card__content">

            <!-- TITLE -->
            <h3 class="wr-product-card__title">
                <a href="<?php the_permalink(); ?>">
                    <?php echo esc_html( $product->get_name() ); ?>
                </a>
            </h3>

            <!-- PRICE -->
            <div class="wr-product-card__price">
                <?php echo wp_kses_post( $product->get_price_html() ); ?>
            </div>

            <!-- ADD TO CART -->
            <div class="wr-product-card__actions">
                <?php woocommerce_template_loop_add_to_cart(); ?>
            </div>

        </div>

    </div>

</li>
