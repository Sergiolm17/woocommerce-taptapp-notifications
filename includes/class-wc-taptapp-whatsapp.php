<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Incluir los archivos necesarios
require_once plugin_dir_path( __FILE__ ) . 'service/message/send.php';
require_once plugin_dir_path( __FILE__ ) . 'service/product/list.php';
require_once plugin_dir_path( __FILE__ ) . 'service/product/create.php';
require_once plugin_dir_path( __FILE__ ) . 'service/product/delete.php';
require_once plugin_dir_path( __FILE__ ) . 'service/product/update.php';

class WC_TapTapp_WhatsApp {

    public static function send_message( $phone, $message, $media = null ) {
        return wc_taptapp_send_message( $phone, $message, $media );
    }

    public static function get_product_list( $phone ) {
        return wc_taptapp_get_product_list( $phone );
    }

    public static function create_product( $product_data ) {
        return wc_taptapp_create_product( $product_data );
    }
    
    public static function delete_product( $product_ids ) {
        return wc_taptapp_delete_product( $product_ids );
    }
    
    public static function update_product( $product_id, $update_data ) {
        return wc_taptapp_update_product( $product_id, $update_data );
    }
}
?>
