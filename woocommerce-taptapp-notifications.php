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
include_once plugin_dir_path( __FILE__ ) . 'includes/notifications.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/whatsapp.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/customer-notes.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';

include_once plugin_dir_path( __FILE__ ) . 'includes/settings/customer-note.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/status.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/invoice.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/settings/core.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/product.php';



require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/product-sync.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/cart-abandonment.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/cf7.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/integrations/product-sync.php';

include_once plugin_dir_path( __FILE__ ) . 'includes/activation.php';
// Initialize the main plugin class
WC_TapTapp_Notifications::init();
WC_TapTapp_Settings::init();
WC_TapTapp_Status_Settings::init();
WC_TapTapp_Customer_Note_Settings::init();
WC_TapTapp_Invoice_Settings::init();
WC_Taptapp_CF7_Integration::init();
WC_TapTapp_Product_Settings::init();
WC_TapTapp_Product_Sync::init();
WC_TapTapp_Customer_Notes::init();
