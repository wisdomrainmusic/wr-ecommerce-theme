<?php
/**
 * WR Single Product Renderer
 * Modern Layout v1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Summary bölümündeki standart Woo öğelerini kaldırıyoruz
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

/**
 * WR Custom Summary Structure
 */
function wr_single_product_structure() {

    global $product;

    echo '<div class="wr-single-wrapper">';

        echo '<div class="wr-single-gallery">';
            wc_get_template( 'single-product/product-image.php' );
        echo '</div>';

        echo '<div class="wr-single-summary">';

            echo '<h1 class="wr-single-title">' . get_the_title() . '</h1>';

            echo '<div class="wr-single-price">';
                echo $product->get_price_html();
            echo '</div>';

            echo '<div class="wr-single-desc">';
                echo wpautop( wp_trim_words( get_the_excerpt(), 25 ) );
            echo '</div>';

            echo '<div class="wr-single-atc">';
                woocommerce_template_single_add_to_cart();
            echo '</div>';

        echo '</div>';

    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'wr_single_product_structure', 5 );
