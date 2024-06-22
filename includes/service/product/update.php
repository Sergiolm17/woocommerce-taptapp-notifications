<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wc_taptapp_update_product( $product_id, $update_data ) {
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


    // Convertir el precio a centavos
    if (isset($update_data['price'])) {
        $update_data['price'] = intval(floatval($update_data['price']) * 1000);
    }


    $args = array(
        'method' => 'PUT',
        'body' => json_encode(array(
            'productId' => $product_id,
            'update' => $update_data
        )),
        'headers' => array(
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key
        ),
        'timeout' => 15 // Establecer tiempo de espera en 15 segundos
    );

    $request_url = rtrim($api_url, '/') . "/product/update"; // Asegúrate de que no haya dobles barras

    // Log de información detallada de la solicitud
    error_log('Updating product on WhatsApp API with URL: ' . $request_url);
    error_log('Request headers: ' . print_r($args['headers'], true));
    error_log('Request body: ' . print_r($args['body'], true));

    $response = wp_remote_request($request_url, $args);

    if ( is_wp_error( $response ) ) {
        error_log('Error during wp_remote_request: ' . $response->get_error_message());
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    } else {
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        error_log('Response code: ' . $response_code);
        error_log('Response body: ' . $response_body);

        $decoded_response = json_decode( $response_body, true );

        if ( $response_code != 200 ) {
            return array(
                'success' => false,
                'message' => 'Error actualizando producto en WhatsApp: ' . $response_body
            );
        }

        return array(
            'success' => true,
            'product' => $decoded_response['productId']
        );
    }
}



?>
