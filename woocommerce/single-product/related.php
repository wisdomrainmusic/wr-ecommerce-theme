<?php
/**
 * Related products
 *
 * @package WR_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( empty( $related_products ) ) {
    return;
}

/**
 * Başlık filtresi Woo standart akışına uyuyor.
 */
$heading = apply_filters(
    'woocommerce_product_related_products_heading',
    __( 'Related products', 'wr-theme' )
);
?>

<section class="wr-related-products-section">
    <?php if ( $heading ) : ?>
        <h2 class="wr-related-title">
            <?php echo esc_html( $heading ); ?>
        </h2>
    <?php endif; ?>

    <?php woocommerce_product_loop_start(); ?>

    <?php foreach ( $related_products as $related_product ) : ?>
        <?php
        $post_object = get_post( $related_product->get_id() );

        if ( ! $post_object ) {
            continue;
        }

        // Woo loop context
        $GLOBALS['post'] = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        setup_postdata( $GLOBALS['post'] );
        ?>

        <li <?php wc_product_class( 'wr-related-product-item', $related_product ); ?>>
            <?php
            /**
             * Burada direkt WR kartı kullanıyoruz:
             * woocommerce/loop/product-card.php
             * Böylece shop/archive ile aynı kart HTML yapısı korunuyor.
             */
            wc_get_template( 'loop/product-card.php' );
            ?>
        </li>

    <?php endforeach; ?>

    <?php woocommerce_product_loop_end(); ?>
</section>

<?php
// Loop global’ini eski haline getir.
wp_reset_postdata();
