<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Customer_Note_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function register_settings() {
        register_setting( 'taptapp_notifications_settings', 'taptapp_notifications_messages', array( __CLASS__, 'sanitize_callback' ) );

        add_settings_section(
            'taptapp_notifications_customer_note_section',
            'Notas de Cliente',
            null,
            'taptapp_notifications_customer_note'
        );

        add_settings_field(
            'taptapp_notifications_customer_note_message',
            'Nota del Cliente',
            array( __CLASS__, 'settings_field_callback' ),
            'taptapp_notifications_customer_note',
            'taptapp_notifications_customer_note_section',
            array(
                'label_for' => 'taptapp_notifications_customer_note_message',
                'status'    => 'customer_note'
            )
        );
    }

    public static function settings_field_callback( $args ) {
        $status = $args['status'];
        $options = get_option( 'taptapp_notifications_messages' );

        $message = isset( $options[ $status ] ) ? $options[ $status ] : "";

        echo "<textarea id='taptapp_notifications_{$status}_message' name='taptapp_notifications_messages[{$status}]' rows='7' cols='50'>{$message}</textarea>";
    }

    public static function sanitize_callback( $input ) {
        $existing_options = get_option( 'taptapp_notifications_messages' );
        if ( ! is_array( $existing_options ) ) {
            $existing_options = array();
        }

        // Ensure $input is an array before merging
        if ( ! is_array( $input ) ) {
            $input = array();
        }

        // Merge existing options with new input, giving precedence to the new input
        $new_options = array_merge( $existing_options, $input );

        return $new_options;
    }

    public static function settings_page() {
        settings_fields( 'taptapp_notifications_settings' );
        do_settings_sections( 'taptapp_notifications_customer_note' );
    }
}

WC_TapTapp_Customer_Note_Settings::init();
?>
