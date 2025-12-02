<?php
/**
 * Header template.
 *
 * @package WR_Ecommerce_Theme
 */

declare( strict_types=1 );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( ! class_exists( 'WR_HB_Render' ) ) : ?>
    <header class="header">
        <div class="container header__inner">
            <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span class="brand__dot" aria-hidden="true"></span>
                <span><?php bloginfo( 'name' ); ?></span>
            </a>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
                <span><?php esc_html_e( 'Menu', 'wr-ecommerce-theme' ); ?></span>
            </button>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'container'      => 'nav',
                    'container_class'=> 'nav',
                    'menu_class'     => 'nav__list',
                    'menu_id'        => 'primary-menu',
                    'fallback_cb'    => '__return_false',
                )
            );
            ?>
            <?php
            // Add mini cart to header
            if ( function_exists( 'wr_get_mini_cart' ) ) {
                wr_get_mini_cart();
            }
            ?>
        </div>
    </header>
<?php endif; ?>
<main class="site-main">
