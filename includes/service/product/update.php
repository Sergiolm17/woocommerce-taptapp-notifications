<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wc_taptapp_update_product( $product_id, $update_data ) {
    $core_settings = get_option( 'taptapp_core_settings' );
    $api_url = isset( $core_settings['taptapp_api_url'] ) ? $core_settings['taptapp_api_url'] : '';
    $api_key = isset( $core_settings['taptapp_api_key'] ) ? $core_settings['taptapp_api_key'] : '';

    $body = array(
        'productId' => $product_id,
        'update' => $update_data
    );

    $args = array(
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key
        )
    );

    $response = wp_remote_request($api_url . "/product/update", array_merge($args, array('method' => 'PUT')));

    if ( is_wp_error( $response ) ) {
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    } else {
        $response_body = wp_remote_retrieve_body( $response );
        $decoded_response = json_decode( $response_body, true );

        if ( isset( $decoded_response['productRes'] ) ) {
            return array(
                'success' => true,
                'product' => $decoded_response['productRes']
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Error actualizando producto en WhatsApp: ' . $response_body
            );
        }
    }
}
?>
