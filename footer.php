<?php
/**
 * Footer template.
 *
 * @package WR_Ecommerce_Theme
 */

declare( strict_types=1 );
?>
</main>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div><?php bloginfo( 'name' ); ?></div>
            <div class="footer__links">
                <a href="<?php echo esc_url( home_url( '/shop' ) ); ?>"><?php esc_html_e( 'Shop', 'wr-ecommerce-theme' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( 'About', 'wr-ecommerce-theme' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'wr-ecommerce-theme' ); ?></a>
            </div>
        </div>
        <p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Crafted for modern online stores.', 'wr-ecommerce-theme' ); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
