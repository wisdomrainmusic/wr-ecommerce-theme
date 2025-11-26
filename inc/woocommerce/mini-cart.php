<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WR Mini Cart Component
 */
function wr_get_mini_cart() {
    ?>
    <div id="wr-mini-cart" class="wr-mini-cart">
        <a href="<?php echo wc_get_cart_url(); ?>" class="wr-cart-icon">
            🛒 <span class="wr-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        </a>

        <div class="wr-mini-cart-dropdown">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
    <?php
}
