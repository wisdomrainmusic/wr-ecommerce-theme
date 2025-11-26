<?php
/**
 * WR Quick View Modal Template
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="wr-quick-view-modal" class="wr-quick-view-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Product quick view', 'wr-theme' ); ?>" style="display: none;">
    <div class="wr-qv-overlay" aria-hidden="true"></div>
    <div class="wr-qv-box" role="document">
        <button class="wr-qv-close" type="button" aria-label="<?php esc_attr_e( 'Close quick view', 'wr-theme' ); ?>">×</button>
        <div class="wr-qv-body" aria-live="polite" aria-busy="false">
            <?php esc_html_e( 'Loading...', 'wr-theme' ); ?>
        </div>
    </div>
</div>
