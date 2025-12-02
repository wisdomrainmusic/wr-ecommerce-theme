<?php
/**
 * WR Header Builder – Admin Page View (B-layout)
 *
 * @package WR_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WR_Theme\Header_Builder\WR_HB_Manager;

$manager         = WR_HB_Manager::get_instance();
$active_layout   = $manager->get_active_layout_id();
$active_layout   = $active_layout ? $active_layout : 'default-header';
$active_layout_data = $manager->get_layout( $active_layout );

// Nonce.
$nonce = wp_create_nonce( 'wr_hb_nonce' );
?>

<div id="wr-hb-app"
     data-layout-id="<?php echo esc_attr( $active_layout ); ?>"
     data-nonce="<?php echo esc_attr( $nonce ); ?>">

    <div class="wr-hb-admin">

        <div class="wr-hb-toolbar">
            <h1 class="wr-hb-title"><?php esc_html_e( 'WR Header Builder', 'wr-ecommerce-theme' ); ?></h1>
            <div class="wr-hb-toolbar-actions">
                <button type="button" class="button" id="wr-hb-reset-layout"><?php esc_html_e( 'Reset', 'wr-ecommerce-theme' ); ?></button>
                <button type="button" class="button button-primary" id="wr-hb-save-layout"><?php esc_html_e( 'Save Header', 'wr-ecommerce-theme' ); ?></button>
            </div>
        </div>

        <div class="wr-hb-main">

            <aside class="wr-hb-widget-pool">
                <h3 class="wr-hb-widget-pool__title">
                    <?php esc_html_e( 'Header Elements', 'wr-ecommerce-theme' ); ?>
                </h3>

                <ul id="wr-hb-widget-list" class="wr-hb-widget-list">
                    <li class="wr-hb-widget-card" data-widget-type="logo" data-widget-label="<?php esc_attr_e( 'Logo', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Logo', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="primary_menu" data-widget-label="<?php esc_attr_e( 'Primary Menu', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Primary Menu', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="secondary_menu" data-widget-label="<?php esc_attr_e( 'Secondary Menu', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Secondary Menu', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="search" data-widget-label="<?php esc_attr_e( 'Search', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Search', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="cart" data-widget-label="<?php esc_attr_e( 'Cart', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Cart', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="button" data-widget-label="<?php esc_attr_e( 'Button', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Button', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="html" data-widget-label="<?php esc_attr_e( 'HTML', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'HTML', 'wr-ecommerce-theme' ); ?>
                    </li>
                    <li class="wr-hb-widget-card" data-widget-type="shortcode" data-widget-label="<?php esc_attr_e( 'Shortcode', 'wr-ecommerce-theme' ); ?>">
                        <?php esc_html_e( 'Shortcode', 'wr-ecommerce-theme' ); ?>
                    </li>
                </ul>
            </aside>

            <section class="wr-hb-layout">

                <div id="wr-hb-rows" class="wr-hb-rows">
                    <!-- JS state → DOM hydrate -->
                </div>

                <button type="button" class="button wr-hb-add-row">
                    + <?php esc_html_e( 'Add Row', 'wr-ecommerce-theme' ); ?>
                </button>

            </section>
        </div>

    </div>

</div>

<?php
// Layout datasını JS'ye geçir.
wp_enqueue_script( 'wr-hb-admin' );
wp_localize_script(
    'wr-hb-admin',
    'wrHbData',
    [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => $nonce,
        'layoutId'  => $active_layout,
        'layout'    => ! empty( $active_layout_data ) ? $active_layout_data : null,
    ]
);
?>
