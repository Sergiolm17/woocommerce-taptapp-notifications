<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Asegurarse de que la función is_plugin_active esté disponible
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class WC_TapTapp_API {

    public static function init() {
        // Verificar si el plugin "WooCommerce Cart Abandonment Recovery" está activo
        if (is_plugin_active('woo-cart-abandonment-recovery/woo-cart-abandonment-recovery.php')) {
            add_action('rest_api_init', array(__CLASS__, 'register_routes'));
        } else {
            //error_log('WooCommerce Cart Abandonment Recovery plugin is not active.');
        }
    }

    public static function register_routes() {
        register_rest_route('taptapp/v1', '/notify', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'handle_notify_request'),
            'permission_callback' => '__return_true',
        ));
    }

    public static function handle_notify_request($request) {
        error_log('Handling notify request...'); // Log de inicio

        $params = $request->get_body_params();
        error_log('Received parameters: ' . print_r($params, true)); // Log de parámetros recibidos
        
        $first_name = sanitize_text_field($params['first_name']);
        $last_name = sanitize_text_field($params['last_name']);
        $email = sanitize_email($params['email']);
        $phone = sanitize_text_field($params['phone']);
        $order_status = sanitize_text_field($params['order_status']);
        $checkout_url = esc_url_raw($params['checkout_url']);
        $coupon_code = sanitize_text_field($params['coupon_code']);
        $product_names = sanitize_text_field($params['product_names']);
        $cart_total = sanitize_text_field($params['cart_total']);
        $product_table = wp_kses_post($params['product_table']);

        // Convertir tabla HTML a texto
        $product_table_text = convert_table_to_text($product_table);
        error_log('Converted product table to text: ' . $product_table_text); // Log de tabla convertida

        // Crear el mensaje de WhatsApp
        $message = "🛒 **¡Hola $first_name!**\n\n" .
                   "Notamos que dejaste algunos productos increíbles en tu carrito y queríamos asegurarnos de que no te los pierdas. ¡Aún están disponibles para ti!\n\n" .
                   "### Productos en tu carrito:\n" .
                   "$product_table_text\n\n" .
                   "**Total del carrito:** $cart_total\n\n" .
                   "🎁 **¡Oferta exclusiva para ti!** Usa el código **$coupon_code** para obtener un **10% de descuento** adicional en tu compra. **¡Solo por tiempo limitado!**\n\n" .
                   "🚀 **COMPLETA TU COMPRA AQUÍ:**\n" .
                   "➡️ [$checkout_url]($checkout_url)\n\n" .
                   "No dejes pasar esta oportunidad de llevarte estos productos a casa. Si tienes alguna pregunta o necesitas ayuda, estamos aquí para asistirte.\n\n" .
                   "¡Gracias por confiar en nosotros!\n" .
                   "**El equipo de TapTapp** ✨";

        error_log('Generated message: ' . $message); // Log de mensaje generado

        // Enviar el mensaje de WhatsApp
        WC_TapTapp_WhatsApp::send_message($phone, $message);
        error_log('Message sent to WhatsApp API'); // Log de mensaje enviado

        return new WP_REST_Response(array('status' => 'success', 'message' => 'Notification sent'), 200);
    }
}

function convert_table_to_text($html) {
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $rows = $dom->getElementsByTagName('tr');
    $text = "";

    $index = 1;
    foreach ($rows as $row) {
        $cols = $row->getElementsByTagName('td');
        if ($cols->length > 0) {
            $item = $cols->item(1)->textContent; // Nombre del producto
            $quantity = $cols->item(2)->textContent; // Cantidad
            $price = $cols->item(3)->textContent; // Precio por unidad
            $subtotal = $cols->item(4)->textContent; // Subtotal
            $text .= "$index. **Producto:** $item\n" .
                     "   - **Cantidad:** $quantity\n" .
                     "   - **Precio:** $price\n" .
                     "   - **Subtotal:** $subtotal\n\n";
            $index++;
        }
    }

    return $text;
}

WC_TapTapp_API::init();
