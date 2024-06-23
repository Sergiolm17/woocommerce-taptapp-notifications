<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Core_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
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

    public static function add_settings_page() {
        add_options_page(
            'TapTapp Core Settings',
            'TapTapp Settings',
            'manage_options',
            'taptapp_notifications_core',
            array( __CLASS__, 'settings_page' )
        );
    }

    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1>Configuración del Core</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'taptapp_core_settings' );
                do_settings_sections( 'taptapp_notifications_core' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

WC_TapTapp_Core_Settings::init();
?>
