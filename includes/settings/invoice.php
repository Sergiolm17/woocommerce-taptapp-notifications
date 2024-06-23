<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Invoice_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function register_settings() {
        register_setting( 'taptapp_notifications_settings', 'taptapp_invoice_settings' );

        add_settings_section(
            'taptapp_invoice_settings_section',
            'Configuración de Facturas PDF y Albaranes',
            null,
            'taptapp_notifications_invoice'
        );

        add_settings_field(
            'taptapp_invoice_message',
            'Mensaje de Factura PDF',
            array( __CLASS__, 'settings_field_callback' ),
            'taptapp_notifications_invoice',
            'taptapp_invoice_settings_section',
            array(
                'label_for' => 'taptapp_invoice_message'
            )
        );

        add_settings_field(
            'taptapp_invoice_statuses',
            'Enviar PDF en los siguientes estados:',
            array( __CLASS__, 'statuses_field_callback' ),
            'taptapp_notifications_invoice',
            'taptapp_invoice_settings_section',
            array(
                'label_for' => 'taptapp_invoice_statuses'
            )
        );

        add_settings_field(
            'taptapp_send_packing_slip',
            'Enviar Albarán',
            array( __CLASS__, 'send_packing_slip_callback' ),
            'taptapp_notifications_invoice',
            'taptapp_invoice_settings_section',
            array(
                'label_for' => 'taptapp_send_packing_slip'
            )
        );

        add_settings_field(
            'taptapp_packing_slip_phone',
            'Número de Teléfono de la Empresa Transportista',
            array( __CLASS__, 'packing_slip_phone_callback' ),
            'taptapp_notifications_invoice',
            'taptapp_invoice_settings_section',
            array(
                'label_for' => 'taptapp_packing_slip_phone'
            )
        );

        add_settings_field(
            'taptapp_packing_slip_status',
            'Enviar Albarán en el siguiente estado:',
            array( __CLASS__, 'packing_slip_status_callback' ),
            'taptapp_notifications_invoice',
            'taptapp_invoice_settings_section',
            array(
                'label_for' => 'taptapp_packing_slip_status'
            )
        );
    }

    public static function settings_field_callback( $args ) {
        $options = get_option( 'taptapp_invoice_settings' );
        $message = isset( $options['taptapp_invoice_message'] ) ? $options['taptapp_invoice_message'] : 'Aquí tienes tu factura PDF. Si tienes alguna pregunta, no dudes en contactarnos.';

        echo "<textarea id='taptapp_invoice_message' name='taptapp_invoice_settings[taptapp_invoice_message]' rows='7' cols='50'>{$message}</textarea>";
    }

    public static function statuses_field_callback( $args ) {
        $options = get_option( 'taptapp_invoice_settings' );
        if ( ! is_array( $options ) ) {
            $options = array();
        }

        $statuses = array(
            'pending' => 'Pendiente de Pago 🕒',
            'processing' => 'Procesando 📦',
            'on-hold' => 'En Espera ⏸️',
            'completed' => 'Completado ✅',
            'cancelled' => 'Cancelado ❌',
            'refunded' => 'Reembolsado 💸',
            'failed' => 'Fallido ⚠️',
            'draft' => 'Borrador 📝'
        );

        foreach ( $statuses as $status => $label ) {
            $checked = isset( $options['taptapp_invoice_statuses'][$status] ) ? 'checked' : '';
            echo "<label><input type='checkbox' name='taptapp_invoice_settings[taptapp_invoice_statuses][{$status}]' value='1' {$checked}> {$label}</label><br>";
        }
    }

    public static function send_packing_slip_callback( $args ) {
        $options = get_option( 'taptapp_invoice_settings' );
        $checked = isset( $options['taptapp_send_packing_slip'] ) ? 'checked' : '';
        echo "<input type='checkbox' id='taptapp_send_packing_slip' name='taptapp_invoice_settings[taptapp_send_packing_slip]' value='1' {$checked}>";
    }

    public static function packing_slip_phone_callback( $args ) {
        $options = get_option( 'taptapp_invoice_settings' );
        $phone = isset( $options['taptapp_packing_slip_phone'] ) ? $options['taptapp_packing_slip_phone'] : '';
        echo "<input type='text' id='taptapp_packing_slip_phone' name='taptapp_invoice_settings[taptapp_packing_slip_phone]' value='{$phone}' class='regular-text'>";
    }

    public static function packing_slip_status_callback( $args ) {
        $options = get_option( 'taptapp_invoice_settings' );
        $selected_status = isset( $options['taptapp_packing_slip_status'] ) ? $options['taptapp_packing_slip_status'] : '';

        $statuses = array(
            'pending' => 'Pendiente de Pago 🕒',
            'processing' => 'Procesando 📦',
            'on-hold' => 'En Espera ⏸️',
            'completed' => 'Completado ✅',
            'cancelled' => 'Cancelado ❌',
            'refunded' => 'Reembolsado 💸',
            'failed' => 'Fallido ⚠️',
            'draft' => 'Borrador 📝'
        );

        echo "<select id='taptapp_packing_slip_status' name='taptapp_invoice_settings[taptapp_packing_slip_status]'>";
        foreach ( $statuses as $status => $label ) {
            $selected = ( $status === $selected_status ) ? 'selected' : '';
            echo "<option value='{$status}' {$selected}>{$label}</option>";
        }
        echo "</select>";
    }

    public static function settings_page() {
        settings_fields( 'taptapp_notifications_settings' );
        do_settings_sections( 'taptapp_notifications_invoice' );
        submit_button();
    }
}

WC_TapTapp_Invoice_Settings::init();
?>
