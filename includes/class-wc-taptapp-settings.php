<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Settings {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles_and_scripts' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function add_settings_page() {
        add_menu_page(
            'TapTapp Notifications',
            'TapTapp Notifications',
            'manage_options',
            'taptapp-notifications',
            array( __CLASS__, 'settings_page' ),
            'dashicons-email-alt',
            56
        );
    }

    public static function enqueue_styles_and_scripts($hook) {
        if ($hook != 'toplevel_page_taptapp-notifications') {
            return;
        }
        wp_enqueue_style('taptapp-admin-styles', plugin_dir_url(__FILE__) . '../css/taptapp-admin-styles.css');
        wp_enqueue_script('taptapp-admin-scripts', plugin_dir_url(__FILE__) . '../js/taptapp-admin-scripts.js', array('jquery'), false, true);
    }

    public static function register_settings() {
        register_setting( 'taptapp_notifications_settings', 'taptapp_notifications_messages' );
        register_setting( 'taptapp_notifications_settings', 'taptapp_invoice_settings' );
    }

    public static function settings_page() {
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'status';
        ?>
        <div class="wrap">
            <h1>Configuración de Notificaciones de TapTapp</h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=taptapp-notifications&tab=status" class="nav-tab <?php echo $tab == 'status' ? 'nav-tab-active' : ''; ?>">Estado del Pedido</a>
                <a href="?page=taptapp-notifications&tab=customer_note" class="nav-tab <?php echo $tab == 'customer_note' ? 'nav-tab-active' : ''; ?>">Notas de Cliente</a>
                <a href="?page=taptapp-notifications&tab=invoice" class="nav-tab <?php echo $tab == 'invoice' ? 'nav-tab-active' : ''; ?>">Facturas PDF</a>
                <a href="?page=taptapp-notifications&tab=core" class="nav-tab <?php echo $tab == 'core' ? 'nav-tab-active' : ''; ?>">Configuración del Core</a>
            </h2>
            <form method="post" action="options.php">
                <?php
                if ($tab == 'status') {
                    settings_fields( 'taptapp_notifications_settings' );
                    WC_TapTapp_Status_Settings::settings_page();
                } elseif ($tab == 'customer_note') {
                    settings_fields( 'taptapp_notifications_settings' );
                    WC_TapTapp_Customer_Note_Settings::settings_page();
                } elseif ($tab == 'invoice') {
                    settings_fields( 'taptapp_notifications_settings' );
                    WC_TapTapp_Invoice_Settings::settings_page();
                } else {
                    settings_fields( 'taptapp_core_settings' );
                    WC_TapTapp_Core_Settings::settings_page();
                }
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

WC_TapTapp_Settings::init();
?>
