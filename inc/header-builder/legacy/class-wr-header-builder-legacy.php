<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WR_Header_Builder_Admin {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register_editor_box' ] );
        add_action( 'save_post', [ $this, 'save_layout' ] );
    }

    public function register_editor_box() {
        add_meta_box(
            'wr_header_builder_editor',
            'Header Layout Builder',
            [ $this, 'render_editor' ],
            'wr_header_builder',
            'normal',
            'high'
        );
    }

    public function render_editor( $post ) {

        // Stored JSON layout
        $json = get_post_meta( $post->ID, '_wr_header_json', true );
        if ( empty( $json ) ) $json = '[]';

        wp_nonce_field( 'wr_header_builder_save', 'wr_header_builder_nonce' );
        ?>

        <style>
            .wr-hb-wrapper { display: flex; gap: 20px; }
            .wr-hb-left { flex: 1; }
            .wr-hb-right { width: 240px; }
            .wr-hb-row { border:1px solid #ddd; margin-bottom:12px; padding:10px; border-radius:6px; background:#fafafa; }
            .wr-hb-row-header { display:flex; justify-content:space-between; margin-bottom:8px; }
            .wr-hb-row-inner { display:flex; gap:10px; }
            .wr-hb-dropzone { flex:1; min-height:60px; border:1px dashed #ccc; padding:8px; background:#fff; border-radius:4px; }
            .wr-hb-widget { padding:6px 10px; background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:6px; cursor:grab; }
            #wr-hb-add-row { margin-bottom:12px; }
        </style>

        <div class="wr-hb-wrapper">

            <!-- LEFT: ROWS -->
            <div class="wr-hb-left">
                <button type="button" id="wr-hb-add-row" class="button button-primary">+ Add Row</button>

                <div id="wr-hb-rows">
                    <?php
                    // JSON’dan UI’yı yeniden kur
                    $layout = json_decode( $json, true );

                    if ( is_array( $layout ) && ! empty( $layout ) ) {
                        foreach ( $layout as $row ) {
                            echo '<div class="wr-hb-row">
                                    <div class="wr-hb-row-header">
                                        <strong>ROW</strong>
                                        <button type="button" class="wr-hb-remove-row">Remove</button>
                                    </div>

                                    <div class="wr-hb-row-inner">';

                            foreach ( ['left','center','right'] as $zone ) {
                                echo '<div class="wr-hb-dropzone" data-zone="'.$zone.'">';

                                if ( isset($row[$zone]) ) {
                                    foreach ( $row[$zone] as $w ) {
                                        echo '<div class="wr-hb-widget">'.$w.'</div>';
                                    }
                                }

                                echo '</div>';
                            }

                            echo '</div></div>';
                        }
                    }
                    ?>
                </div>

                <!-- Save JSON -->
                <input type="hidden" id="wr_header_json" name="wr_header_json" value="<?php echo esc_attr($json); ?>">
            </div>

            <!-- RIGHT: WIDGET PANEL -->
            <div class="wr-hb-right">
                <strong>Widgets</strong>
                <div id="wr-hb-widget-list">
                    <div class="wr-hb-widget">Logo</div>
                    <div class="wr-hb-widget">Menu</div>
                    <div class="wr-hb-widget">Search</div>
                    <div class="wr-hb-widget">Cart</div>
                    <div class="wr-hb-widget">Account</div>
                    <div class="wr-hb-widget">Button</div>
                    <div class="wr-hb-widget">HTML Block</div>
                    <div class="wr-hb-widget">Spacer</div>
                </div>
            </div>

        </div>
        <?php
    }

    public function save_layout( $post_id ) {

        if ( get_post_type( $post_id ) !== 'wr_header_builder' ) return;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if ( ! isset($_POST['wr_header_builder_nonce']) ||
             ! wp_verify_nonce($_POST['wr_header_builder_nonce'], 'wr_header_builder_save') )
            return;

        if ( isset($_POST['wr_header_json']) ) {
            update_post_meta($post_id, '_wr_header_json', wp_unslash($_POST['wr_header_json']));
        }
    }
}
