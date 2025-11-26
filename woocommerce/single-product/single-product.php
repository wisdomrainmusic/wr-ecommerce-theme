<?php
/**
 * Single Product Template v1
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

echo '<div class="wr-single-product">';
    wc_get_template_part( 'content', 'single-product' );
echo '</div>';

get_footer( 'shop' );
