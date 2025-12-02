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

        // Mevcut JSON
        $json = get_post_meta( $post->ID, '_wr_header_json', true );
        if ( empty( $json ) ) {
            $json = '[]';
        }

        // Nonce
        wp_nonce_field( 'wr_header_builder_save', 'wr_header_builder_nonce' );
        ?>
        <div class="wr-hb-wrapper">

            <!-- Gizli JSON alanı (Form save için) -->
            <input type="hidden"
                   id="wr_header_json"
                   name="wr_header_json"
                   value="<?php echo esc_attr( $json ); ?>" />

            <!-- JS uygulamasının bağlanacağı alan -->
            <div id="wr-header-builder-app"
                 data-json="<?php echo esc_attr( $json ); ?>">
            </div>
        </div>
        <?php
    }

    public function save_layout( $post_id ) {

        // Post type kontrolü
        if ( get_post_type( $post_id ) !== 'wr_header_builder' ) {
            return;
        }

        // Autosave / permission kontrolü basit tutuyoruz
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! isset( $_POST['wr_header_builder_nonce'] ) ||
             ! wp_verify_nonce( $_POST['wr_header_builder_nonce'], 'wr_header_builder_save' ) ) {
            return;
        }

        if ( isset( $_POST['wr_header_json'] ) ) {
            $json = wp_unslash( $_POST['wr_header_json'] );
            update_post_meta( $post_id, '_wr_header_json', $json );
        }
    }
}
