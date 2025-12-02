<?php
/**
 * WR Header Builder AJAX endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WR_HB_Ajax {
    /**
     * Manager instance.
     *
     * @var WR_HB_Manager
     */
    protected $manager;

    /**
     * Singleton instance.
     *
     * @var WR_HB_Ajax|null
     */
    protected static $instance = null;

    /**
     * Constructor.
     */
    protected function __construct() {
        $this->manager = WR_HB_Manager::get_instance();
    }

    /**
     * Singleton accessor.
     */
    public static function get_instance(): WR_HB_Ajax {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Wire AJAX hooks.
     */
    public function init(): void {
        add_action( 'wp_ajax_wr_hb_save_layout', [ $this, 'handle_save' ] );
        add_action( 'wp_ajax_wr_hb_get_layout', [ $this, 'handle_get' ] );
    }

    /**
     * Save a layout from AJAX.
     */
    public function handle_save(): void {
        check_ajax_referer( 'wr_hb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied', 'wr-ecommerce-theme' ) ], 403 );
        }

        $layout_id = isset( $_POST['layout_id'] ) ? sanitize_text_field( wp_unslash( $_POST['layout_id'] ) ) : '';
        $raw_data  = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
        $decoded   = json_decode( $raw_data, true );

        if ( empty( $layout_id ) || ! is_array( $decoded ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid payload', 'wr-ecommerce-theme' ) ], 400 );
        }

        $decoded['rows'] = isset( $decoded['rows'] ) && is_array( $decoded['rows'] ) ? $decoded['rows'] : [];

        if ( ! $this->has_valid_structure( $decoded ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid layout schema', 'wr-ecommerce-theme' ) ], 400 );
        }

        $this->manager->save_layout( $layout_id, $decoded );
        $this->manager->set_active_layout_id( $layout_id );

        wp_send_json_success(
            [
                'layout'       => $this->manager->get_layout( $layout_id ),
                'activeLayout' => $this->manager->get_active_layout_id(),
            ]
        );
    }

    /**
     * Return a layout via AJAX.
     */
    public function handle_get(): void {
        check_ajax_referer( 'wr_hb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied', 'wr-ecommerce-theme' ) ], 403 );
        }

        $layout_id = isset( $_GET['layout_id'] ) ? sanitize_text_field( wp_unslash( $_GET['layout_id'] ) ) : '';

        if ( '' === $layout_id ) {
            wp_send_json_error( [ 'message' => __( 'Missing layout id', 'wr-ecommerce-theme' ) ], 400 );
        }

        wp_send_json_success(
            [
                'layout'       => $this->manager->get_layout( $layout_id ),
                'activeLayout' => $this->manager->get_active_layout_id(),
            ]
        );
    }

    /**
     * Minimal schema validation for layout structure.
     */
    protected function has_valid_structure( array $layout ): bool {
        if ( ! isset( $layout['rows'] ) || ! is_array( $layout['rows'] ) ) {
            return false;
        }

        foreach ( $layout['rows'] as $row ) {
            if ( ! isset( $row['columns'] ) || ! is_array( $row['columns'] ) ) {
                return false;
            }

            foreach ( $row['columns'] as $column ) {
                if ( ! isset( $column['widgets'] ) || ! is_array( $column['widgets'] ) ) {
                    return false;
                }
            }
        }

        return true;
    }
}
