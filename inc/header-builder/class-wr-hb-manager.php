<?php
/**
 * WR Header Builder Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WR_HB_Manager {
    /**
     * Singleton instance.
     *
     * @var WR_HB_Manager|null
     */
    protected static $instance = null;

    /**
     * Option key for active layout id.
     *
     * @var string
     */
    protected $active_option_key = 'wr_hb_active_layout';

    /**
     * Default layout ids per device.
     *
     * @var array
     */
    protected $default_layout_ids = [
        'desktop' => 'layout-desktop',
        'tablet'  => 'layout-tablet',
        'mobile'  => 'layout-mobile',
    ];

    /**
     * Plugin/theme version for cache busting.
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Constructor.
     */
    protected function __construct() {
        $this->ensure_default_active_layout();
    }

    /**
     * Get singleton instance.
     */
    public static function get_instance(): WR_HB_Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Return version string used for cache busting.
     */
    public function get_version(): string {
        return $this->version;
    }

    /**
     * Fetch all device layouts keyed by device.
     */
    public function get_layouts(): array {
        $layouts = [];

        foreach ( $this->default_layout_ids as $device => $layout_id ) {
            $layouts[ $device ] = $this->get_layout( $layout_id );
        }

        return $layouts;
    }

    /**
     * Fetch single layout by id from option storage.
     */
    public function get_layout( string $id ): array {
        $option_key = 'wr_hb_layout_' . $id;
        $raw        = get_option( $option_key );
        $layout     = [];

        if ( is_string( $raw ) && ! empty( $raw ) ) {
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                $layout = $decoded;
            }
        } elseif ( is_array( $raw ) ) {
            $layout = $raw;
        }

        $device = $layout['device'] ?? $this->infer_device_from_id( $id );

        if ( empty( $layout ) ) {
            $layout = $this->default_layout( $device );
        }

        $normalized = $this->normalize_layout( $layout, $device, $id );

        if ( empty( $raw ) ) {
            $this->save_layout( $id, $normalized );
        }

        return $normalized;
    }

    /**
     * Persist a single layout into JSON option storage.
     */
    public function save_layout( string $id, array $data ): bool {
        $device   = $data['device'] ?? $this->infer_device_from_id( $id );
        $validated = $this->normalize_layout( $data, $device, $id );

        return update_option( 'wr_hb_layout_' . $id, wp_json_encode( $validated ) );
    }

    /**
     * Convenience method to persist multiple layouts keyed by device.
     */
    public function save_layouts( array $layouts ): void {
        foreach ( $this->default_layout_ids as $device => $layout_id ) {
            $layout = $layouts[ $device ] ?? [];
            if ( ! is_array( $layout ) ) {
                $layout = [];
            }

            if ( empty( $layout['id'] ) ) {
                $layout['id'] = $layout_id;
            }

            $layout['device'] = $layout['device'] ?? $device;
            $this->save_layout( $layout['id'], $layout );
        }
    }

    /**
     * Get active layout id.
     */
    public function get_active_layout_id(): ?string {
        $active = get_option( $this->active_option_key );

        if ( is_string( $active ) && '' !== $active ) {
            return $active;
        }

        return $this->default_layout_ids['desktop'] ?? null;
    }

    /**
     * Set active layout id.
     */
    public function set_active_layout_id( string $id ): bool {
        if ( '' === $id ) {
            return false;
        }

        return update_option( $this->active_option_key, $id );
    }

    /**
     * Normalize incoming layout with required keys.
     */
    public function normalize_layout( array $layout, string $device, string $id ): array {
        $layout['id']     = $layout['id'] ?? $id;
        $layout['name']   = $layout['name'] ?? ucfirst( $device ) . ' Header';
        $layout['device'] = $device;
        $layout['rows']   = isset( $layout['rows'] ) && is_array( $layout['rows'] ) ? array_values( $layout['rows'] ) : [];

        foreach ( $layout['rows'] as $r_index => $row ) {
            $row['id']       = $row['id'] ?? uniqid( 'row_', true );
            $row['order']    = (int) ( $row['order'] ?? ( $r_index + 1 ) );
            $row['settings'] = isset( $row['settings'] ) && is_array( $row['settings'] ) ? $row['settings'] : [];
            $row['columns']  = isset( $row['columns'] ) && is_array( $row['columns'] ) ? array_values( $row['columns'] ) : [];

            foreach ( $row['columns'] as $c_index => $column ) {
                $column['id']       = $column['id'] ?? uniqid( 'col_', true );
                $column['order']    = (int) ( $column['order'] ?? ( $c_index + 1 ) );
                $column['width']    = $column['width'] ?? '33%';
                $column['settings'] = isset( $column['settings'] ) && is_array( $column['settings'] ) ? $column['settings'] : [];
                $column['widgets']  = isset( $column['widgets'] ) && is_array( $column['widgets'] ) ? array_values( $column['widgets'] ) : [];

                foreach ( $column['widgets'] as $w_index => $widget ) {
                    $widget['id']       = $widget['id'] ?? uniqid( 'wg_', true );
                    $widget['type']     = $widget['type'] ?? 'html';
                    $widget['order']    = (int) ( $widget['order'] ?? ( $w_index + 1 ) );
                    $widget['settings'] = isset( $widget['settings'] ) && is_array( $widget['settings'] ) ? $widget['settings'] : [];

                    $column['widgets'][ $w_index ] = $widget;
                }

                $row['columns'][ $c_index ] = $column;
            }

            $layout['rows'][ $r_index ] = $row;
        }

        return $layout;
    }

    /**
     * Ensure an active layout id exists.
     */
    protected function ensure_default_active_layout(): void {
        if ( ! get_option( $this->active_option_key ) ) {
            update_option( $this->active_option_key, $this->default_layout_ids['desktop'] );
        }
    }

    /**
     * Infer device type from layout id.
     */
    protected function infer_device_from_id( string $id ): string {
        if ( false !== strpos( $id, 'tablet' ) ) {
            return 'tablet';
        }

        if ( false !== strpos( $id, 'mobile' ) ) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Default layout structure.
     *
     * header_layout = [
     *   'id'      => string,
     *   'name'    => string,
     *   'device'  => 'desktop'|'tablet'|'mobile',
     *   'rows'    => [
     *     [
     *       'id'       => string,
     *       'order'    => int,
     *       'settings' => [],
     *       'columns'  => [
     *         [
     *           'id'       => string,
     *           'order'    => int,
     *           'width'    => string,
     *           'settings' => [],
     *           'widgets'  => [
     *             [
     *               'id'       => string,
     *               'type'     => string,
     *               'order'    => int,
     *               'settings' => [],
     *             ],
     *           ],
     *         ],
     *       ],
     *     ],
     *   ],
     * ];
     */
    public function default_layout( string $device ): array {
        return [
            'id'      => 'layout-' . $device,
            'name'    => ucfirst( $device ) . ' Header',
            'device'  => $device,
            'rows'    => [
                [
                    'id'       => 'row-1-' . $device,
                    'order'    => 1,
                    'settings' => [],
                    'columns'  => [
                        [
                            'id'       => 'col-1-' . $device,
                            'order'    => 1,
                            'width'    => '25%',
                            'settings' => [],
                            'widgets'  => [
                                [
                                    'id'       => 'wg-logo-' . $device,
                                    'type'     => 'logo',
                                    'order'    => 1,
                                    'settings' => [],
                                ],
                            ],
                        ],
                        [
                            'id'       => 'col-2-' . $device,
                            'order'    => 2,
                            'width'    => '50%',
                            'settings' => [],
                            'widgets'  => [
                                [
                                    'id'       => 'wg-menu-' . $device,
                                    'type'     => 'menu',
                                    'order'    => 1,
                                    'settings' => [],
                                ],
                            ],
                        ],
                        [
                            'id'       => 'col-3-' . $device,
                            'order'    => 3,
                            'width'    => '25%',
                            'settings' => [],
                            'widgets'  => [
                                [
                                    'id'       => 'wg-cart-' . $device,
                                    'type'     => 'cart',
                                    'order'    => 1,
                                    'settings' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

/**
 * Global helper to get Header Builder manager instance.
 */
function wr_hb_manager(): WR_HB_Manager {
    return WR_HB_Manager::get_instance();
}

/**
 * Render header builder output.
 */
function wr_hb_render_header( string $device = 'desktop' ): void {
    $render = class_exists( 'WR_HB_Render' ) ? WR_HB_Render::get_instance() : null;

    if ( $render ) {
        $render->output_header();
    }
}
