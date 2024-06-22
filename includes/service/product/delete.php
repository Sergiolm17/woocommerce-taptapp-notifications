<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wc_taptapp_delete_product( $product_ids ) {
    $core_settings = get_option( 'taptapp_core_settings' );
    $api_url = isset( $core_settings['taptapp_api_url'] ) ? $core_settings['taptapp_api_url'] : '';
    $api_key = isset( $core_settings['taptapp_api_key'] ) ? $core_settings['taptapp_api_key'] : '';

    if (empty($api_url) || empty($api_key)) {
        error_log('API URL o API Key no están configurados.');
        return array(
            'success' => false,
            'message' => 'API URL o API Key no están configurados.'
        );
    }

    $body = array(
        'productIds' => $product_ids
    );

    $args = array(
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key
        ),
        'timeout' => 15
    );

    $response = wp_remote_post($api_url . "/product/delete", $args);

    if ( is_wp_error( $response ) ) {
        error_log('Error during wp_remote_post: ' . $response->get_error_message());
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    } else {
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );
        $decoded_response = json_decode( $response_body, true );

        if ( $response_code != 200 ) {
            return array(
                'success' => false,
                'message' => 'Error eliminando producto en WhatsApp: ' . $response_body
            );
        }

        return array(
            'success' => true,
            'message' => 'Producto eliminado en WhatsApp: ' . $response_body
        );
    }
}
?>
