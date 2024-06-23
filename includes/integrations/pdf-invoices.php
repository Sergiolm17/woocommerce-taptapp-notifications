<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Asegurarse de que la función is_plugin_active esté disponible
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class WC_TapTapp_PDF_Invoices {

    public static function init() {
        add_action( 'woocommerce_order_status_changed', array( __CLASS__, 'send_documents_if_plugin_installed' ), 10, 4 );
    }

    public static function send_documents_if_plugin_installed( $order_id, $old_status, $new_status, $order ) {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        if ( ! is_plugin_active( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' ) ) {
            // PDF Invoices & Packing Slips plugin is not installed or activated
            error_log( 'PDF Invoices & Packing Slips plugin is not active' );
            return;
        }

        $options = get_option( 'taptapp_invoice_settings' );
        if ( ! is_array( $options ) ) {
            $options = array();
        }

        // Send invoice PDF if the status matches
        $invoice_statuses = isset( $options['taptapp_invoice_statuses'] ) ? array_keys( $options['taptapp_invoice_statuses'] ) : array();
        if ( in_array( $new_status, $invoice_statuses ) ) {
            self::send_pdf_invoice( $order, $options );
        }

        // Send packing slip if enabled and the status matches
        $send_packing_slip = isset( $options['taptapp_send_packing_slip'] ) ? $options['taptapp_send_packing_slip'] : false;
        $packing_slip_status = isset( $options['taptapp_packing_slip_status'] ) ? $options['taptapp_packing_slip_status'] : '';
        if ( $send_packing_slip && $new_status === $packing_slip_status ) {
            self::send_packing_slip( $order, $options );
        }
    }

    private static function send_pdf_invoice( $order, $options ) {
        // Get the PDF URL
        $pdf_url = self::get_pdf_invoice_url( $order->get_id() );
        error_log( 'PDF URL: ' . $pdf_url );

        if ( $pdf_url ) {
            $message = isset( $options['taptapp_invoice_message'] ) ? $options['taptapp_invoice_message'] : "Aquí tienes tu factura PDF. Si tienes alguna pregunta, no dudes en contactarnos.";
            $phone = $order->get_billing_phone();

            WC_TapTapp_WhatsApp::send_message( $phone, $message, array(
                'document_url' => $pdf_url,
                'fileName' => 'factura.pdf',
                'mimetype' => 'application/pdf'
            ));
        }
    }

    private static function send_packing_slip( $order, $options ) {
        // Get the packing slip URL
        $packing_slip_url = self::get_packing_slip_url( $order->get_id() );
        error_log( 'Packing Slip URL: ' . $packing_slip_url );

        if ( $packing_slip_url ) {
            $phone = isset( $options['taptapp_packing_slip_phone'] ) ? $options['taptapp_packing_slip_phone'] : '';

            if ( $phone ) {
                $message = "Aquí tienes el albarán para el pedido #{$order->get_id()}.";
                WC_TapTapp_WhatsApp::send_message( $phone, $message, array(
                    'document_url' => $packing_slip_url,
                    'fileName' => 'albaran.pdf',
                    'mimetype' => 'application/pdf'
                ));
            }
        }
    }

    private static function get_pdf_invoice_url( $order_id ) {
        // Generate the PDF invoice URL using the WooCommerce PDF Invoices & Packing Slips plugin
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return false;
        }

        $order_key = $order->get_order_key();
        $pdf_nonce = wp_create_nonce( 'wpo_wcpdf' );

        $pdf_url = add_query_arg( array(
            'action' => 'generate_wpo_wcpdf',
            'document_type' => 'invoice',
            'order_ids' => $order_id,
            'access_key' => $order_key,
            'pdf-nonce' => $pdf_nonce,
        ), admin_url( 'admin-ajax.php' ) );

        return $pdf_url;
    }

    private static function get_packing_slip_url( $order_id ) {
        // Generate the packing slip URL using the WooCommerce PDF Invoices & Packing Slips plugin
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return false;
        }

        $order_key = $order->get_order_key();
        $pdf_nonce = wp_create_nonce( 'wpo_wcpdf' );

        $packing_slip_url = add_query_arg( array(
            'action' => 'generate_wpo_wcpdf',
            'document_type' => 'packing-slip',
            'order_ids' => $order_id,
            'access_key' => $order_key,
            'pdf-nonce' => $pdf_nonce,
        ), admin_url( 'admin-ajax.php' ) );

        return $packing_slip_url;
    }
}

WC_TapTapp_PDF_Invoices::init();
?>
