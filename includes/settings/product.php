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
        echo '<h2>Panel de Configuración de Productos</h2>';
        
        // Botones para navegar a otros paneles de configuración
        echo '<div class="settings-buttons">';
        echo '<a href="' . admin_url('admin.php?page=taptapp-notifications-status') . '" class="button button-primary">Estado del Pedido</a> ';
        echo '<a href="' . admin_url('admin.php?page=taptapp-notifications-customer_note') . '" class="button button-primary">Notas de Cliente</a> ';
        echo '<a href="' . admin_url('admin.php?page=taptapp-notifications-invoice') . '" class="button button-primary">Facturas PDF</a> ';
        echo '<a href="' . admin_url('admin.php?page=taptapp-notifications-core') . '" class="button button-primary">Configuración del Core</a> ';
        echo '</div>';

        settings_fields( 'taptapp_notifications_settings' );
        do_settings_sections( 'taptapp_notifications_product' );
    }
}

WC_TapTapp_Product_Settings::init();
?>
