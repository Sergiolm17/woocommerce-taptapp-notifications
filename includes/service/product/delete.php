<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wc_taptapp_delete_product( $product_ids ) {
    $core_settings = get_option( 'taptapp_core_settings' );
    $api_url = isset( $core_settings['taptapp_api_url'] ) ? $core_settings['taptapp_api_url'] : '';
    $api_key = isset( $core_settings['taptapp_api_key'] ) ? $core_settings['taptapp_api_key'] : '';

    $body = array(
        'productIds' => $product_ids
    );

    $args = array(
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key
        )
    );

    $response = wp_remote_post($api_url . "/product/delete", $args);

    if ( is_wp_error( $response ) ) {
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    } else {
        $response_body = wp_remote_retrieve_body( $response );
        $decoded_response = json_decode( $response_body, true );

        if ( isset( $decoded_response['success'] ) && $decoded_response['success'] == true ) {
            return array(
                'success' => true,
                'message' => 'Producto(s) eliminado(s) correctamente.'
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Error eliminando producto(s) en WhatsApp: ' . $response_body
            );
        }
    }
}
?>
