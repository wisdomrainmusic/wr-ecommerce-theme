<?php
/**
 * The template for displaying all pages
 *
 * @package WR_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();

        // Elementor ve klasik editor içerik alanı
        the_content();

    endwhile; // End of the loop.
    ?>
</main>

<?php
get_footer();
