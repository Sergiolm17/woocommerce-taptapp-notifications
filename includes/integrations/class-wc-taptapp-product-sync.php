<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Product_Sync {

    public static function init() {
        add_action('save_post_product', array(__CLASS__, 'handle_product_save'), 10, 3);
        add_action('wp_trash_post', array(__CLASS__, 'handle_product_trash'));
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

        // Verifica si el producto está publicado
        if ($post->post_status !== 'publish') {
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

    public static function handle_product_trash($post_id) {
        // Verifica si es un producto de WooCommerce
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Verifica si el producto tiene un ID de producto de WhatsApp
        $whatsapp_product_id = get_post_meta($post_id, '_whatsapp_product_id', true);
        if (!$whatsapp_product_id) {
            return;
        }

        // Actualiza el producto en WhatsApp para ocultarlo
        $product = wc_get_product($post_id);
        if (!$product) {
            return;
        }

        $update_data = self::get_product_data($product);
        $update_data['isHidden'] = true;

        error_log('Updating WhatsApp product with ID to hide: ' . $whatsapp_product_id);
        error_log('Update data: ' . print_r($update_data, true));

        $response = wc_taptapp_update_product($whatsapp_product_id, $update_data);
        if (!$response['success']) {
            error_log('Error updating product on WhatsApp to hide: ' . $response['message']);
        } else {
            error_log('Product updated on WhatsApp to hide: ' . print_r($response['product'], true));
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
        $sale_price = get_post_meta($product->get_id(), '_sale_price', true);
        $regular_price = get_post_meta($product->get_id(), '_regular_price', true);
        $price = $sale_price ? $sale_price : $regular_price;
        
        $description = $product->get_short_description() ? $product->get_short_description() : $product->get_description();

        $product_data = array(
            'name' => $product->get_name(),
            'currency' => get_woocommerce_currency(),
            'description' => $product->get_description() ? $product->get_description() : 'Descripción no disponible',
            'price' => $price ? intval($price * 1000) : 0,
            'url' => stripslashes(html_entity_decode($product->get_permalink())),
            'isHidden' => !$product->is_visible(),
            'originCountryCode' => 'PE',
            'images' => array_map(function($image_id) {
                return array('url' => stripslashes(html_entity_decode(wp_get_attachment_url($image_id))));
            }, $product->get_gallery_image_ids())
        );

        if (empty($product_data['images'])) {
            $product_data['images'] = array(
                array(
                    'url' => get_site_url() . '/wp-content/uploads/woocommerce-placeholder.png'
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
        if ($response['success'] && isset($response['product']['id'])) {
            update_post_meta($product_id, '_whatsapp_product_id', $response['product']['id']);
            error_log('Product created on WhatsApp: ' . print_r($response['product']['id'], true));
        } else {
            error_log('Error creating product on WhatsApp: ' . $response['message']);
        }
    }

    private static function handle_product_update($product_id, $whatsapp_product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $update_data = self::get_product_data($product);

        error_log('Updating WhatsApp product with ID: ' . $whatsapp_product_id);
        error_log('Update data: ' . print_r($update_data, true));

        $response = wc_taptapp_update_product($whatsapp_product_id, $update_data);
        if (!$response['success']) {
            error_log('Error updating product on WhatsApp: ' . $response['message']);
        } else {
            error_log('Product updated on WhatsApp: ' . print_r($response['product'], true));
        }
    }
}

WC_TapTapp_Product_Sync::init();

?>
