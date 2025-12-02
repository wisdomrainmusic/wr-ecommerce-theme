<?php
/**
 * Header Builder admin page view.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e( 'WR Header Builder', 'wr-ecommerce-theme' ); ?></h1>
    <div
        class="wr-hb-admin"
        id="wr-hb-app"
        data-nonce="<?php echo esc_attr( $nonce ); ?>"
        data-layout-id="<?php echo esc_attr( $active_layout_id ); ?>"
        data-layout='<?php echo esc_attr( wp_json_encode( $layout ) ); ?>'
    >
        <div class="wr-hb-toolbar">
            <button class="button button-primary" id="wr-hb-save-layout"><?php esc_html_e( 'Save Header', 'wr-ecommerce-theme' ); ?></button>
            <button class="button" id="wr-hb-reset-layout"><?php esc_html_e( 'Reset', 'wr-ecommerce-theme' ); ?></button>
        </div>

        <div class="wr-hb-main">
            <aside class="wr-hb-widget-pool">
                <h3 class="wr-hb-widget-pool__title"><?php esc_html_e( 'Header Elements', 'wr-ecommerce-theme' ); ?></h3>
                <ul id="wr-hb-widget-list" class="wr-hb-widget-list">
                    <?php foreach ( $widgets as $type => $widget ) : ?>
                        <li
                            class="wr-hb-widget-card"
                            data-widget-type="<?php echo esc_attr( $type ); ?>"
                            data-widget-default-settings='<?php echo esc_attr( wp_json_encode( $widget['default_settings'] ) ); ?>'
                        >
                            <span class="wr-hb-widget-card__label"><?php echo esc_html( $widget['label'] ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <section class="wr-hb-layout">
                <div id="wr-hb-rows" class="wr-hb-rows"></div>
                <button type="button" class="button wr-hb-add-row" id="wr-hb-add-row">+ <?php esc_html_e( 'Add Row', 'wr-ecommerce-theme' ); ?></button>
            </section>
        </div>
    </div>
</div>
