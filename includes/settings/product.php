<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Product_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function register_settings() {
        register_setting( 'taptapp_notifications_settings', 'taptapp_product_settings' );


    }


    public static function settings_page() {
        settings_fields( 'taptapp_notifications_settings' );
        do_settings_sections( 'taptapp_notifications_product' );
        submit_button();
    }
}

WC_TapTapp_Product_Settings::init();
?>
