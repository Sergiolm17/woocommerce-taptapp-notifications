<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_TapTapp_Product_Sync {

    public static function init() {
        add_action('transition_post_status', array(__CLASS__, 'on_product_publish'), 10, 3);
        error_log('WC_TapTapp_Product_Sync initialized');
    }

    public static function on_product_publish($new_status, $old_status, $post) {
        if ($post->post_type !== 'product') {
            return;
        }

        if ($new_status === 'publish' && $old_status !== 'publish') {
            error_log('Product created: ' . $post->ID);
            self::handle_product_creation($post->ID);
        }
    }

    private static function handle_product_creation($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $product_data = array(
            'name' => $product->get_name(),
            'currency' => get_woocommerce_currency(),
            'description' => $product->get_description(),
            'price' => $product->get_price() ? $product->get_price() : 0,
            'url' => $product->get_permalink(),
            'isHidden' => !$product->is_visible(),
            'originCountryCode' => 'PE',
            'images' => array_map(function($image_id) {
                return array('url' => wp_get_attachment_url($image_id));
            }, $product->get_gallery_image_ids())
        );

        if (empty($product_data['images'])) {
            $product_data['images'] = array(
                array(
                    'url' => 'https://ik.imagekit.io/fresa/IMAGEN%202%20PRODUCT.jpg?updatedAt=1718330824262'
                )
            );
        }

        error_log('Product data to create: ' . print_r($product_data, true));

        $response = wc_taptapp_create_product($product_data);
        if (!$response['success']) {
            error_log('Error creating product on WhatsApp: ' . $response['message']);
        } else {
            error_log('Product created on WhatsApp: ' . print_r($response['product'], true));

            // Guardar el ID de WhatsApp como metadato del producto
            if (isset($response['product']['id'])) {
                update_post_meta($product_id, '_whatsapp_product_id', $response['product']['id']);
                error_log('WhatsApp product ID saved: ' . $response['product']['id']);
            }
        }
    }
}

WC_TapTapp_Product_Sync::init();

?>
