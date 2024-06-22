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

    $request_url = $api_url . "/product/delete";

    // Log de información detallada de la solicitud
    error_log('Deleting product on WhatsApp API with URL: ' . $request_url);
    error_log('Request headers: ' . print_r($args['headers'], true));
    error_log('Request body: ' . print_r($body, true));

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

        error_log('Response code: ' . $response_code);
        error_log('Response body: ' . $response_body);

        $decoded_response = json_decode( $response_body, true );

        if ( $response_code != 200 ) {
            return array(
                'success' => false,
                'message' => 'Error eliminando producto en WhatsApp: ' . $response_body
            );
        }

        // Revisar la respuesta y loguear si hay algún error adicional
        if (isset($decoded_response['error'])) {
            error_log('Error adicional eliminando producto en WhatsApp: ' . $response_body);
            return array(
                'success' => false,
                'message' => 'Error adicional eliminando producto en WhatsApp: ' . $response_body
            );
        }

        return array(
            'success' => true,
            'message' => 'Producto eliminado en WhatsApp: ' . $response_body
        );
    }
}
?>
