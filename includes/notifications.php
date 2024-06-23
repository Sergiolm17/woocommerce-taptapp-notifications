<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Notifications {

    public static function init() {
        // Hook to WooCommerce order status changed
        add_action( 'woocommerce_order_status_changed', array( __CLASS__, 'send_whatsapp_notification' ), 10, 4 );

        // Hook to WooCommerce new customer note
        add_action( 'woocommerce_new_customer_note', array( 'WC_TapTapp_Customer_Notes', 'send_whatsapp_customer_note' ), 10, 1 );
    }

    public static function send_whatsapp_notification( $order_id, $old_status, $new_status, $order ) {
        $message = self::get_message_for_status( $order, $new_status );

        if ( $message ) {
            $phone = $order->get_billing_phone();
    
            $response = WC_TapTapp_WhatsApp::send_message( $phone, $message );
    
            // Manejar la respuesta y añadir una nota al pedido
            if ( $response['success'] ) {
                $order->add_order_note( $response['message'], false );
            } else {
                $order->add_order_note( 'Error enviando mensaje de WhatsApp: ' . $response['message'], false );
            }
        }
    }

    private static function get_message_for_status( $order, $status ) {
        $options = get_option( 'taptapp_notifications_messages' );
        $message_template = isset( $options[ $status ] ) ? $options[ $status ] : '';
    
        if ( ! $message_template ) {
            return null;
        }
    
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $billing_address = $order->get_formatted_billing_address();
        $shipping_address = $order->get_formatted_shipping_address();
        $order_date = wc_format_datetime( $order->get_date_created() );
        $order_status = wc_get_order_status_name( $order->get_status() );
        $customer_note = $order->get_customer_note();
        $payment_url = $order->get_checkout_payment_url();
        $customer_email = $order->get_billing_email();
        $phone_number = $order->get_billing_phone();
        $currency_symbol = get_woocommerce_currency_symbol();
        $total = $order->get_total();
        $order_total_tax = $order->get_total_tax();
        $payment_method = $order->get_payment_method_title();
        $shipping_method = $order->get_shipping_method();
        $shipping_cost = $order->get_shipping_total();
        $coupon_codes = implode(', ', $order->get_coupon_codes());
        $discount_total = $order->get_discount_total();
        $order_notes = implode("\n", array_map(function($note) {
            return $note->comment_content;
        }, $order->get_customer_order_notes()));
        $store_name = get_bloginfo('name');
        $store_url = home_url();
        $order_subtotal = $order->get_subtotal();
        $billing_company = $order->get_billing_company();
        $shipping_company = $order->get_shipping_company();
    
        $line_items = $order->get_items();
        $items = array();
        foreach ( $line_items as $item ) {
            $items[] = '🔸 ' . $item->get_quantity() . 'x ' . $item->get_name();
        }
        $items_string = implode("\n", $items);
    
        $search = array(
            '{first_name}', '{last_name}', '{billing_address}', '{shipping_address}', 
            '{order_date}', '{order_status}', '{customer_note}', '{payment_url}', 
            '{customer_email}', '{phone_number}', '{currency_symbol}', '{total}', 
            '{order_total_tax}', '{payment_method}', '{shipping_method}', 
            '{shipping_cost}', '{coupon_codes}', '{discount_total}', '{order_notes}', 
            '{store_name}', '{store_url}', '{order_subtotal}', '{billing_company}', 
            '{shipping_company}', '{items}', '{order_id}'
        );
    
        $replace = array(
            $first_name, $last_name, $billing_address, $shipping_address, 
            $order_date, $order_status, $customer_note, $payment_url, 
            $customer_email, $phone_number, $currency_symbol, $total, 
            $order_total_tax, $payment_method, $shipping_method, 
            $shipping_cost, $coupon_codes, $discount_total, $order_notes, 
            $store_name, $store_url, $order_subtotal, $billing_company, 
            $shipping_company, $items_string, $order->get_id()
        );
    
        $message = str_replace( $search, $replace, $message_template );
    
        return $message;
    }

}

WC_TapTapp_Notifications::init();