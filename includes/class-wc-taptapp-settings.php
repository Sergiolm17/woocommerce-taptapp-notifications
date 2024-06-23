<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Settings {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_pages' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles_and_scripts' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function add_settings_pages() {
        add_menu_page(
            'TapTapp Notifications',
            'TapTapp Notifications',
            'manage_options',
            'taptapp-notifications',
            array( 'WC_TapTapp_Product_Settings', 'settings_page' ),
            'dashicons-email-alt',
            56
        );

        add_submenu_page(
            'taptapp-notifications',
            'Estado del Pedido',
            'Estado del Pedido',
            'manage_options',
            'taptapp-notifications-status',
            array( 'WC_TapTapp_Status_Settings', 'settings_page' )
        );

        add_submenu_page(
            'taptapp-notifications',
            'Notas de Cliente',
            'Notas de Cliente',
            'manage_options',
            'taptapp-notifications-customer_note',
            array( 'WC_TapTapp_Customer_Note_Settings', 'settings_page' )
        );

        add_submenu_page(
            'taptapp-notifications',
            'Facturas PDF',
            'Facturas PDF',
            'manage_options',
            'taptapp-notifications-invoice',
            array( 'WC_TapTapp_Invoice_Settings', 'settings_page' )
        );

        add_submenu_page(
            'taptapp-notifications',
            'Configuración del Core',
            'Configuración del Core',
            'manage_options',
            'taptapp-notifications-core',
            array( 'WC_TapTapp_Core_Settings', 'settings_page' )
        );

        add_submenu_page(
            'taptapp-notifications',
            'Producto',
            'Producto',
            'manage_options',
            'taptapp-notifications-product',
            array( 'WC_TapTapp_Product_Settings', 'settings_page' )
        );
    }

    public static function redirect_to_status_page() {
        wp_redirect(admin_url('admin.php?page=taptapp-notifications-status'));
        exit;
    }

    public static function enqueue_styles_and_scripts($hook) {
        if (strpos($hook, 'taptapp-notifications') === false) {
            return;
        }
        wp_enqueue_style('taptapp-admin-styles', plugin_dir_url(__FILE__) . '../css/taptapp-admin-styles.css');
        wp_enqueue_script('taptapp-admin-scripts', plugin_dir_url(__FILE__) . '../js/taptapp-admin-scripts.js', array('jquery'), false, true);
    }

    public static function register_settings() {
        register_setting( 'taptapp_notifications_settings', 'taptapp_notifications_messages' );
        register_setting( 'taptapp_notifications_settings', 'taptapp_invoice_settings' );
        register_setting( 'taptapp_notifications_settings', 'taptapp_product_settings' ); // Añadir la configuración del producto
    }
}

WC_TapTapp_Settings::init();
?>
