<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function taptapp_notifications_activate() {
    $default_messages = array(
        'pending' => 'Tu pedido está pendiente de pago. 🕒',
        'processing' => 'Tu pedido está siendo procesado. 📦',
        'on-hold' => 'Tu pedido está en espera. ⏸️',
        'completed' => 'Tu pedido ha sido completado. ✅',
        'cancelled' => 'Tu pedido ha sido cancelado. ❌',
        'refunded' => 'Tu pedido ha sido reembolsado. 💸',
        'failed' => 'Tu pedido ha fallado. ⚠️',
        'draft' => 'Tu pedido está en borrador. 📝',
        'customer_note' => '📢 Nota del Pedido 📢\n\n¡{customer_note}'
    );

    $existing_messages = get_option( 'taptapp_notifications_messages', array() );

    // Verificar y añadir cualquier mensaje faltante
    foreach ($default_messages as $key => $message) {
        if ( ! array_key_exists($key, $existing_messages) ) {
            $existing_messages[$key] = $message;
        }
    }

    update_option( 'taptapp_notifications_messages', $existing_messages );
}

register_activation_hook( __FILE__, 'taptapp_notifications_activate' );
