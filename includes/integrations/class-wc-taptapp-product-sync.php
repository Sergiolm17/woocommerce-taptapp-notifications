<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Product_Sync {

    public static function init() {
        add_action('save_post_product', array(__CLASS__, 'handle_product_save'), 10, 3);
        add_action('before_delete_post', array(__CLASS__, 'handle_product_delete'));
    }

    public static function handle_product_save($post_id, $post, $update) {
        // Verifica si es una revisión
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Verifica si es un producto de WooCommerce
        if ($post->post_type !== 'product') {
            return;
        }

        // Verifica si el producto ya tiene un ID de producto de WhatsApp
        $whatsapp_product_id = get_post_meta($post_id, '_whatsapp_product_id', true);
        
        if ($update && $whatsapp_product_id) {
            self::handle_product_update($post_id, $whatsapp_product_id);
        } else {
            self::handle_product_create($post_id);
        }
    }

    public static function handle_product_delete($post_id) {
        // Verifica si es un producto de WooCommerce
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Verifica si el producto tiene un ID de producto de WhatsApp
        $whatsapp_product_id = get_post_meta($post_id, '_whatsapp_product_id', true);
        if (!$whatsapp_product_id) {
            return;
        }

        // Llama a la función para eliminar el producto en WhatsApp
        $response = wc_taptapp_delete_product(array($whatsapp_product_id));
        if (!$response['success']) {
            error_log('Error eliminando producto en WhatsApp: ' . $response['message']);
        } else {
            error_log('Producto eliminado en WhatsApp: ' . print_r($response, true));
        }
    }

    private static function get_product_data($product) {
        $product_data = array(
            'name' => $product->get_name(),
            'currency' => get_woocommerce_currency(),
            'description' => $product->get_description(),
            'price' => $product->get_price() ? intval($product->get_price()) : 0,
            'url' => html_entity_decode($product->get_permalink()),
            'isHidden' => !$product->is_visible(),
            'originCountryCode' => 'PE',
            'images' => array_map(function($image_id) {
                return array('url' => html_entity_decode(wp_get_attachment_url($image_id)));
            }, $product->get_gallery_image_ids())
        );

        if (empty($product_data['images'])) {
            $product_data['images'] = array(
                array(
                    'url' => 'https://ik.imagekit.io/fresa/IMAGEN%202%20PRODUCT.jpg?updatedAt=1718330824262'
                )
            );
        }

        $sku = $product->get_sku();
        if (!empty($sku)) {
            $product_data['sku'] = $sku;
        }

        return $product_data;
    }

    private static function handle_product_create($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $product_data = self::get_product_data($product);

        $response = wc_taptapp_create_product($product_data);
        if ($response['success']) {
            update_post_meta($product_id, '_whatsapp_product_id', $response['product']['productId']);
        } else {
            // Aquí podrías agregar una notificación al administrador o manejar el error de manera más específica.
        }
    }

    private static function handle_product_update($product_id, $whatsapp_product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $update_data = self::get_product_data($product);

        $response = wc_taptapp_update_product($whatsapp_product_id, $update_data);
        if (!$response['success']) {
            // Aquí podrías agregar una notificación al administrador o manejar el error de manera más específica.
        }
    }
}

WC_TapTapp_Product_Sync::init();

?>
