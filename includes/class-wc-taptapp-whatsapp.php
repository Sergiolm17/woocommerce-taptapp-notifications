<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_WhatsApp {

    public static function send_message( $phone, $message, $media = null ) {
        $api_url = isset( $core_settings['taptapp_api_url'] ) ? $core_settings['taptapp_api_url'] : '';
        $api_key = isset( $core_settings['taptapp_api_key'] ) ? $core_settings['taptapp_api_key'] : '';


        $body = array(
            'jid' => $phone,
            'type' => 'number',
            'message' => array()
        );

        if ( $media ) {
            if (isset($media['document_url'])) {
                $body['message']['document'] = array(
                    'url' => $media['document_url']
                );
                if (isset($media['fileName'])) {
                    $body['message']['fileName'] = $media['fileName'];
                }
                if (isset($media['mimetype'])) {
                    $body['message']['mimetype'] = $media['mimetype'];
                }
                if (!empty($message)) {
                    $body['message']['caption'] = $message;
                }
            } elseif (isset($media['image_url'])) {
                $body['message']['image'] = array(
                    'url' => $media['image_url']
                );
                if (!empty($message)) {
                    $body['message']['caption'] = $message;
                }
            }
        } else {
            $body['message']['text'] = $message;
        }
        error_log('Sending WhatsApp message with body: ' . json_encode($body));

        $args = array(
            'body' => json_encode($body),
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $api_key
            )
        );

        $response = wp_remote_post($api_url, $args);

        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        } else {
            $response_body = wp_remote_retrieve_body( $response );
            $decoded_response = json_decode( $response_body, true );

            if ( isset( $decoded_response['status'] ) && $decoded_response['status'] == 'PENDING' ) {
                return array(
                    'success' => true,
                    'message' => 'Mensaje de WhatsApp enviado correctamente. Estado: PENDING. ID del mensaje: ' . $decoded_response['key']['id']
                );
            } else {
                return array(
                    'success' => false,
                    'message' => 'Error enviando mensaje de WhatsApp: ' . $response_body
                );
            }
        }
    }
}
