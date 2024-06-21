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

        add_settings_section(
            'taptapp_product_settings_section',
            'Configuración de Productos',
            null,
            'taptapp_notifications_product'
        );

        add_settings_field(
            'taptapp_product_api_url',
            'Product API URL',
            array( __CLASS__, 'api_url_field_callback' ),
            'taptapp_notifications_product',
            'taptapp_product_settings_section',
            array(
                'label_for' => 'taptapp_product_api_url'
            )
        );

        add_settings_field(
            'taptapp_product_api_key',
            'API Key',
            array( __CLASS__, 'api_key_field_callback' ),
            'taptapp_notifications_product',
            'taptapp_product_settings_section',
            array(
                'label_for' => 'taptapp_product_api_key'
            )
        );
    }

    public static function api_url_field_callback( $args ) {
        $options = get_option( 'taptapp_product_settings' );
        $api_url = isset( $options['taptapp_product_api_url'] ) ? $options['taptapp_product_api_url'] : '';
        echo "<input type='text' id='taptapp_product_api_url' name='taptapp_product_settings[taptapp_product_api_url]' value='{$api_url}' class='regular-text'>";
    }

    public static function api_key_field_callback( $args ) {
        $options = get_option( 'taptapp_product_settings' );
        $api_key = isset( $options['taptapp_product_api_key'] ) ? $options['taptapp_product_api_key'] : '';
        echo "<input type='password' id='taptapp_product_api_key' name='taptapp_product_settings[taptapp_product_api_key]' value='{$api_key}' class='regular-text'>";
    }

    public static function settings_page() {
        settings_fields( 'taptapp_notifications_settings' );
        do_settings_sections( 'taptapp_notifications_product' );
        submit_button();
    }
}

WC_TapTapp_Product_Settings::init();
?>
