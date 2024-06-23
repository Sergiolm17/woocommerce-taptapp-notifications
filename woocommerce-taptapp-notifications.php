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

