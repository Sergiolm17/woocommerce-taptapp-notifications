<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wc_taptapp_get_product_list( $phone ) {
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
        'jid' => $phone,
    );

    $args = array(
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key
        )
    );

    $request_url = rtrim($api_url, '/') . "/product/list"; // Asegúrate de que no haya dobles barras

    $response = wp_remote_post($request_url, $args);

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
                'message' => 'Error obteniendo la lista de productos: ' . $response_body
            );
        }

        if ( isset( $decoded_response['products'] ) ) {
            // Eliminar las imageUrls de cada producto y convertir el precio
            foreach ($decoded_response['products'] as &$product) {
                unset($product['imageUrls']);
                // Convertir el precio de centavos a unidad monetaria
                if (isset($product['price'])) {
                    $product['price'] = floatval($product['price']) / 100;
                }
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
