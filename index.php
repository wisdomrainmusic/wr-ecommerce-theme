<?php
/**
 * Main template file.
 *
 * @package WR_Ecommerce_Theme
 */

declare( strict_types=1 );

get_header();
?>
<section class="cards">
    <div class="container">
        <header class="cards__header">
            <h1 class="cards__title"><?php esc_html_e( 'Latest posts', 'wr-ecommerce-theme' ); ?></h1>
        </header>
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
                        <h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="card__meta">
                            <span><?php echo esc_html( get_the_date() ); ?></span>
                            <span><?php echo esc_html( get_the_author() ); ?></span>
                        </div>
                        <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
                    </article>
                    <?php
                }
            } else {
                echo '<p>' . esc_html__( 'No posts found.', 'wr-ecommerce-theme' ) . '</p>';
            }
            ?>
        </div>
        <?php the_posts_pagination(); ?>
    </div>
</section>
<?php
get_footer();
