<?php
/**
 * WR Shop Renderer
 * Forces WooCommerce to use custom product card template
 */

defined( 'ABSPATH' ) || exit;

function wr_shop_custom_product_card() {
    wc_get_template( 'loop/product-card.php', array(), '', get_template_directory() . '/woocommerce/' );
}
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

add_action( 'woocommerce_before_shop_loop_item', 'wr_shop_custom_product_card' );
