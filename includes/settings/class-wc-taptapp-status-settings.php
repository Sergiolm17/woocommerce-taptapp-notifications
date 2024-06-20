<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Status_Settings {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function register_settings() {
        register_setting( 'taptapp_notifications_settings', 'taptapp_notifications_messages', array( __CLASS__, 'sanitize_callback' ) );

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
            add_settings_section(
                "taptapp_notifications_{$status}_section",
                '',
                null,
                "taptapp_notifications_{$status}"
            );

            add_settings_field(
                "taptapp_notifications_{$status}_message",
                $label,
                array( __CLASS__, 'settings_field_callback' ),
                "taptapp_notifications_{$status}",
                "taptapp_notifications_{$status}_section",
                array(
                    'label_for' => "taptapp_notifications_{$status}_message",
                    'status'    => $status
                )
            );
        }
    }

    public static function settings_field_callback( $args ) {
        $status = $args['status'];
        $options = get_option( 'taptapp_notifications_messages' );

        $message = isset( $options[ $status ] ) ? $options[ $status ] : "";

        $placeholders = array(
            '{first_name}', '{last_name}', '{billing_address}', '{shipping_address}', 
            '{order_date}', '{order_status}', '{customer_note}', '{payment_url}', 
            '{customer_email}', '{phone_number}', '{currency_symbol}', '{total}', 
            '{order_total_tax}', '{payment_method}', '{shipping_method}', 
            '{shipping_cost}', '{coupon_codes}', '{discount_total}', '{order_notes}', 
            '{store_name}', '{store_url}', '{order_subtotal}', '{billing_company}', 
            '{shipping_company}', '{items}', '{order_id}'
        );

        echo '<div class="placeholder-buttons">';
        foreach ( $placeholders as $placeholder ) {
            echo '<button type="button" class="insert-placeholder button" data-placeholder="' . esc_attr( $placeholder ) . '">' . esc_html( $placeholder ) . '</button> ';
        }
        echo '</div>';

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
        $subtab = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : 'pending';
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=taptapp-notifications&tab=status&subtab=pending" class="nav-tab <?php echo $subtab == 'pending' ? 'nav-tab-active' : ''; ?>">Pendiente de Pago 🕒</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=processing" class="nav-tab <?php echo $subtab == 'processing' ? 'nav-tab-active' : ''; ?>">Procesando 📦</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=on-hold" class="nav-tab <?php echo $subtab == 'on-hold' ? 'nav-tab-active' : ''; ?>">En Espera ⏸️</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=completed" class="nav-tab <?php echo $subtab == 'completed' ? 'nav-tab-active' : ''; ?>">Completado ✅</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=cancelled" class="nav-tab <?php echo $subtab == 'cancelled' ? 'nav-tab-active' : ''; ?>">Cancelado ❌</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=refunded" class="nav-tab <?php echo $subtab == 'refunded' ? 'nav-tab-active' : ''; ?>">Reembolsado 💸</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=failed" class="nav-tab <?php echo $subtab == 'failed' ? 'nav-tab-active' : ''; ?>">Fallido ⚠️</a>
            <a href="?page=taptapp-notifications&tab=status&subtab=draft" class="nav-tab <?php echo $subtab == 'draft' ? 'nav-tab-active' : ''; ?>">Borrador 📝</a>
        </h2>
        <?php
        settings_fields( 'taptapp_notifications_settings' );
        do_settings_sections( "taptapp_notifications_{$subtab}" );
    }
}

WC_TapTapp_Status_Settings::init();
?>
