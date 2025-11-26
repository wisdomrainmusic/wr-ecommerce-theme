<?php
/**
 * Product Card v1
 * Custom WR E-Commerce Theme product loop card
 */

defined( 'ABSPATH' ) || exit;

global $product;

?>
<div class="wr-product-card">
    <a href="<?php the_permalink(); ?>" class="wr-product-thumb">
        <?php echo $product->get_image(); ?>
    </a>

    <h3 class="wr-product-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h3>

    <div class="wr-product-price">
        <?php echo $product->get_price_html(); ?>
    </div>

    <div class="wr-product-actions">
        <div class="wr-add-to-cart">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>

        <?php do_action( 'wr_quick_view_button' ); ?>
    </div>
</div>
