<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Core_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function register_settings() {
        register_setting( 'taptapp_core_settings', 'taptapp_core_settings' );

        add_settings_section(
            'taptapp_core_settings_section',
            'Configuración del Core',
            null,
            'taptapp_notifications_core'
        );

        add_settings_field(
            'taptapp_api_url',
            'API URL',
            array( __CLASS__, 'api_url_field_callback' ),
            'taptapp_notifications_core',
            'taptapp_core_settings_section',
            array(
                'label_for' => 'taptapp_api_url'
            )
        );

        add_settings_field(
            'taptapp_api_key',
            'API Key',
            array( __CLASS__, 'api_key_field_callback' ),
            'taptapp_notifications_core',
            'taptapp_core_settings_section',
            array(
                'label_for' => 'taptapp_api_key'
            )
        );
    }

    public static function api_url_field_callback( $args ) {
        $options = get_option( 'taptapp_core_settings' );
        $api_url = isset( $options['taptapp_api_url'] ) ? $options['taptapp_api_url'] : '';
        echo "<input type='text' id='taptapp_api_url' name='taptapp_core_settings[taptapp_api_url]' value='{$api_url}' class='regular-text'>";
    }

    public static function api_key_field_callback( $args ) {
        $options = get_option( 'taptapp_core_settings' );
        $api_key = isset( $options['taptapp_api_key'] ) ? $options['taptapp_api_key'] : '';
        echo "<input type='password' id='taptapp_api_key' name='taptapp_core_settings[taptapp_api_key]' value='{$api_key}' class='regular-text'>";
    }

    public static function settings_page() {
        settings_fields( 'taptapp_core_settings' );
        do_settings_sections( 'taptapp_notifications_core' );
    }
}

WC_TapTapp_Core_Settings::init();
?>
