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
        $json = get_post_meta( $post->ID, '_wr_header_json', true );
        ?>
        <div id="wr-header-builder-app" data-json='<?php echo esc_attr( $json ); ?>'></div>
        <?php
    }

    public function save_layout( $post_id ) {
        if ( isset( $_POST['wr_header_json'] ) ) {
            update_post_meta( $post_id, '_wr_header_json', wp_unslash( $_POST['wr_header_json'] ) );
        }
    }
}
