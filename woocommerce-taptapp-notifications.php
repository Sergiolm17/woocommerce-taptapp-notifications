<?php

/**
 * Plugin Name: WooCommerce TapTapp Notifications
 * Description: Enviar notificaciones de WhatsApp basadas en el estado de los pedidos de WooCommerce y cuando se añaden notas al cliente.
 * Version: 1.1.1
 * Author: TapTapp
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include the necessary files
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-taptapp-notifications.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-taptapp-whatsapp.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-taptapp-customer-notes.php';

include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-taptapp-settings.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/class-wc-taptapp-customer-note-settings.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/class-wc-taptapp-status-settings.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/class-wc-taptapp-invoice-settings.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/class-wc-taptapp-core-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/class-wc-taptapp-product-settings.php';



require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/class-wc-taptapp-integrations.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/class-wc-taptapp-cart-abandonment-integration.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/class-wc-taptapp-cf7-integration.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/class-wc-taptapp-product-sync.php';


// Initialize the main plugin class
WC_TapTapp_Notifications::init();
WC_TapTapp_Settings::init();
WC_TapTapp_Status_Settings::init();
WC_TapTapp_Customer_Note_Settings::init();
WC_TapTapp_Invoice_Settings::init();
WC_Taptapp_CF7_Integration::init();
WC_TapTapp_Product_Settings::init();
WC_TapTapp_Product_Sync::init();


// Activation hook to set default messages
function taptapp_notifications_activate() {
    $default_messages = array(
        'pending' => 'Tu pedido está pendiente de pago. 🕒',
        'processing' => 'Tu pedido está siendo procesado. 📦',
        'on-hold' => 'Tu pedido está en espera. ⏸️',
        'completed' => 'Tu pedido ha sido completado. ✅',
        'cancelled' => 'Tu pedido ha sido cancelado. ❌',
        'refunded' => 'Tu pedido ha sido reembolsado. 💸',
        'failed' => 'Tu pedido ha fallado. ⚠️',
        'draft' => 'Tu pedido está en borrador. 📝',
        'customer_note' => '📢 Nota del Pedido 📢\n\n¡{customer_note}'
    );

    $existing_messages = get_option( 'taptapp_notifications_messages', array() );

    // Verificar y añadir cualquier mensaje faltante
    foreach ($default_messages as $key => $message) {
        if ( ! array_key_exists($key, $existing_messages) ) {
            $existing_messages[$key] = $message;
        }
    }

    update_option( 'taptapp_notifications_messages', $existing_messages );
}

register_activation_hook( __FILE__, 'taptapp_notifications_activate' );
