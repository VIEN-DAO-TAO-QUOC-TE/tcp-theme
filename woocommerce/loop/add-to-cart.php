<?php
/**
 * Loop Add to Cart — TEMPORARY OVERRIDE
 *
 * Tạm thời chưa bán khóa học → button "Xem thêm" + permalink thay vì add-to-cart.
 * Khi mở bán lại: revert về template gốc của WooCommerce (xem `woocommerce/templates/loop/add-to-cart.php`).
 *
 * @package WooCommerce\Templates
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product instanceof WC_Product) {
    return;
}

// Loại bỏ các class WC add-to-cart, giữ class theme styling (button, is-flat, primary, is-small, mb-0...)
$class = isset($args['class']) ? (string) $args['class'] : 'button';
$class = preg_replace('/\b(add_to_cart_button|ajax_add_to_cart|product_type_\w+)\b/', '', $class);
$class = trim(preg_replace('/\s+/', ' ', $class));

echo apply_filters(
    'woocommerce_loop_add_to_cart_link',
    sprintf(
        '<a href="%s" class="%s">%s</a>',
        esc_url(get_permalink($product->get_id())),
        esc_attr($class),
        esc_html__('Xem thêm', 'tcp-theme')
    ),
    $product,
    $args
);
