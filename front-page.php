<?php
/**
 * Front page template.
 *
 * @package WR_Ecommerce_Theme
 */

declare( strict_types=1 );

get_header();
?>
<section class="hero">
    <div class="container hero__surface">
        <div>
            <div class="hero__badge">
                <span aria-hidden="true">⚡</span>
                <?php esc_html_e( 'Ecommerce Ready', 'wr-ecommerce-theme' ); ?>
            </div>
            <h1 class="hero__title"><?php esc_html_e( 'Launch a modern store in minutes.', 'wr-ecommerce-theme' ); ?></h1>
            <p class="hero__text"><?php esc_html_e( 'Promote featured collections, convert visitors with beautiful CTAs, and keep shoppers engaged with a clean, responsive layout.', 'wr-ecommerce-theme' ); ?></p>
            <div class="hero__cta">
                <a class="button" href="<?php echo esc_url( home_url( '/shop' ) ); ?>"><?php esc_html_e( 'Shop collection', 'wr-ecommerce-theme' ); ?></a>
                <a class="button button--ghost" href="#shop"><?php esc_html_e( 'Browse highlights', 'wr-ecommerce-theme' ); ?></a>
            </div>
        </div>
        <div class="hero__media">
            <?php
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'large' );
            } else {
                echo '<img src="' . esc_url( get_template_directory_uri() . '/screenshot.png' ) . '" alt="' . esc_attr__( 'Store preview', 'wr-ecommerce-theme' ) . '" />';
            }
            ?>
        </div>
    </div>
</section>

<section class="cards" id="shop">
    <div class="container">
        <div class="cards__header">
            <h2 class="cards__title"><?php esc_html_e( 'Featured items', 'wr-ecommerce-theme' ); ?></h2>
            <a class="button button--ghost" href="<?php echo esc_url( home_url( '/shop' ) ); ?>"><?php esc_html_e( 'View all', 'wr-ecommerce-theme' ); ?></a>
        </div>
        <div class="cards__grid">
            <?php
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'card__thumb' ) ); ?>
                        <?php else : ?>
                            <div class="card__thumb" aria-hidden="true"></div>
                        <?php endif; ?>
                        <h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="card__meta">
                            <span><?php echo esc_html( get_the_date() ); ?></span>
                            <span class="card__price"><?php esc_html_e( '$49', 'wr-ecommerce-theme' ); ?></span>
                        </div>
                    </article>
                    <?php
                }
            } else {
                ?>
                <p><?php esc_html_e( 'Add products or posts to feature them here.', 'wr-ecommerce-theme' ); ?></p>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<section class="container callout">
    <h2 class="callout__title"><?php esc_html_e( 'Ready for your next promotion?', 'wr-ecommerce-theme' ); ?></h2>
    <p class="callout__text"><?php esc_html_e( 'Pair this theme with your favorite ecommerce plugin, customize colors, and publish conversion-focused landing pages in no time.', 'wr-ecommerce-theme' ); ?></p>
    <a class="button" href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Talk with us', 'wr-ecommerce-theme' ); ?></a>
</section>
<?php
get_footer();
