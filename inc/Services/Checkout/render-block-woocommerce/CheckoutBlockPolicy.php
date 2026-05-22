<?php

namespace TCP\Theme\Services\Checkout;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CheckoutBlockPolicy
{
    use Singleton;

    protected function init(): void
    {
        // 1) Force attribute của checkout-actions-block (ẩn Return to Cart + đổi label nút)
        add_filter('render_block_data', [$this, 'filter_block_data'], 10, 1);

        // 2) Prepend “Trở về giỏ hàng” lên đầu (trước block checkout)
        // add_filter('render_block_woocommerce/checkout', [$this, 'prepend_back_link'], 10, 2);
        add_filter('render_block_woocommerce/checkout', [$this, 'prepend_back_link'], 10, 2);

        // 3) Thêm class vào body để scope CSS (optional)
        add_filter('body_class', [$this, 'body_class']);


        add_filter('render_block', [$this, 'wpsites_additional_information_block'], 10, 2);
    }
    public function wpsites_additional_information_block($block_content, $block)
    {

        // echo "<pre>";
        // var_export($block);
        // echo "</pre>";

        if (isset($block['blockName']) && $block['blockName'] === 'woocommerce/checkout-additional-information-block') {

            $content = '<div>Custom HTML After Additional Information Block</div>';

            return $content;
        }

        return $block_content;
    }

    public function body_class(array $classes): array
    {
        if (!is_admin() && function_exists('is_checkout') && is_checkout()) {
            $classes[] = 'tcp-checkout';
        }
        return $classes;
    }

    public function filter_block_data(array $parsed_block): array
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) {
            return $parsed_block;
        }

        // Chỉ đụng vào block Actions của Checkout
        if (($parsed_block['blockName'] ?? '') === 'woocommerce/checkout-actions-block') {
            // Ẩn link Return to cart (để nút Place Order tự fullWidth)
            $parsed_block['attrs']['showReturnToCart'] = false;

            // Optional: đổi text nút đặt hàng theo thiết kế
            $parsed_block['attrs']['placeOrderButtonLabel'] = 'Thanh toán ngay';

            // Optional: thêm class để CSS target (nếu cần)
            $existing = $parsed_block['attrs']['className'] ?? '';
            $parsed_block['attrs']['className'] = trim($existing . ' tcp-checkout-actions');
        }

        return $parsed_block;
    }

    public function prepend_back_link(string $html, array $block): string
    {

        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) {
            return $html;
        }

        // Không hiện ở trang order received (optional)
        if (function_exists('is_order_received_page') && is_order_received_page()) {
            return $html;
        }

        $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
        if (!$cart_url) {
            return $html;
        }

        $label = 'Trở về giỏ hàng';

        $back = sprintf(
            '<div class="tcp-checkout-back">
                <a class="tcp-checkout-back__link" href="%s">← %s</a>
             </div>',
            esc_url($cart_url),
            esc_html($label)
        );

        // Prepend trước checkout block => nằm ở đầu trang đúng yêu cầu
        return $back . $html;
    }
}
