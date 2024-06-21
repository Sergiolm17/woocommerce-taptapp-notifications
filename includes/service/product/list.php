<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wc_taptapp_get_product_list( $phone ) {
    $core_settings = get_option( 'taptapp_core_settings' );
    $api_url = isset( $core_settings['taptapp_api_url'] ) ? $core_settings['taptapp_api_url'] : '';
    $api_key = isset( $core_settings['taptapp_api_key'] ) ? $core_settings['taptapp_api_key'] : '';

    $body = array(
        'jid' => $phone,
    );

    $args = array(
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key
        )
    );

    $response = wp_remote_post($api_url . "/product/list", $args);

    if ( is_wp_error( $response ) ) {
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    } else {
        $response_body = wp_remote_retrieve_body( $response );
        $decoded_response = json_decode( $response_body, true );

        if ( isset( $decoded_response['products'] ) ) {
            // Eliminar las imageUrls de cada producto
            foreach ($decoded_response['products'] as &$product) {
                unset($product['imageUrls']);
            }

            return array(
                'success' => true,
                'products' => $decoded_response['products'],
                'nextPageCursor' => isset($decoded_response['nextPageCursor']) ? $decoded_response['nextPageCursor'] : ''
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Error obteniendo la lista de productos: ' . $response_body
            );
        }
    }
}
?>
