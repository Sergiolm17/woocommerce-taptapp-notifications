<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Incluir el archivo send.php
require_once plugin_dir_path( __FILE__ ) . 'service/message/send.php';
require_once plugin_dir_path( __FILE__ ) . 'service/product/list.php';


class WC_TapTapp_WhatsApp {

    public static function send_message( $phone, $message, $media = null ) {
        return wc_taptapp_send_message( $phone, $message, $media );
    }

    public static function get_product_list( $phone ) {
        return wc_taptapp_get_product_list( $phone );
    }
}
?>
