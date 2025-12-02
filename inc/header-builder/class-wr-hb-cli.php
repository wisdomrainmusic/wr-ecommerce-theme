<?php
/**
 * Placeholder WP-CLI skeleton for WR Header Builder (şimdilik pasif).
 *
 * Önerilen komutlar (tamamı yorum olarak bırakıldı):
 *
 * wp wr_hb export --layout=<id> --file=<path>
 * wp wr_hb import --file=<path>
 *
 * Kullanım notu:
 * - Bu sınıf henüz yüklenmiyor; include/require eklenmediği için aktif değil.
 * - İleride WP_CLI\add_command ile aktive edilebilir.
 */

// if ( defined( 'WP_CLI' ) && WP_CLI ) {
//     /**
//      * Class WR_HB_CLI
//      *
//      * Basit export/import iskeleti.
//      */
//     class WR_HB_CLI extends WP_CLI_Command {
//         /**
//          * Export header builder layout to JSON file.
//          *
//          * ## OPTIONS
//          *
//          * <layout>
//          * : Layout ID (örn: layout-desktop).
//          *
//          * <file>
//          * : Yazılacak dosya yolu.
//          */
//         public function export( $args, $assoc_args ) {
//             list( $layout_id, $file ) = $args;
//
//             $layout  = function_exists( 'wr_hb_manager' ) ? wr_hb_manager()->get_layout( $layout_id ) : [];
//             $written = file_put_contents( $file, wp_json_encode( $layout, JSON_PRETTY_PRINT ) );
//
//             if ( false === $written ) {
//                 WP_CLI::error( 'Dosya yazılamadı.' );
//             }
//
//             WP_CLI::success( 'Layout export edildi: ' . $file );
//         }
//
//         /**
//          * Import header builder layout from JSON file.
//          */
//         public function import( $args, $assoc_args ) {
//             list( $file ) = $args;
//
//             if ( ! file_exists( $file ) ) {
//                 WP_CLI::error( 'Dosya bulunamadı.' );
//             }
//
//             $json = file_get_contents( $file );
//             $data = json_decode( $json, true );
//
//             if ( ! is_array( $data ) || empty( $data['id'] ) ) {
//                 WP_CLI::error( 'Geçersiz layout verisi.' );
//             }
//
//             if ( function_exists( 'wr_hb_manager' ) ) {
//                 wr_hb_manager()->save_layout( $data['id'], $data );
//             }
//
//             WP_CLI::success( 'Layout import edildi: ' . $data['id'] );
//         }
//     }
//
//     WP_CLI::add_command( 'wr_hb', 'WR_HB_CLI' );
// }
