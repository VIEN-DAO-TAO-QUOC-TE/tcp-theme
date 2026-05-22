<?php

namespace TCP\Theme\Services\Checkout;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CheckoutLayoutPolicy
{
    use Singleton;

    protected function init(): void
    {
        add_filter('render_block_data', [$this, 'filter_block_tree'], 10, 1);
        // add_filter('render_block_woocommerce/checkout', [$this, 'prepend_back_link'], 10, 2);
        add_filter('render_block_woocommerce/checkout-contact-information-block', [$this, 'prepend_back_link'], 10, 2);

        // add_filter('render_block_woocommerce/checkout', function ($html) {
        //     if (is_admin() || !function_exists('is_checkout') || !is_checkout()) return $html;
        //     return '<div class="tcp-checkout-skin">' . $html . '</div>';
        // }, 10, 1);

        add_action('init', function () {
          
            // add_filter('woocommerce_checkout_fields', [$this, 'intelligent_customize_checkout_fields']);
        });
    }
    public function intelligent_customize_checkout_fields($fields)
    {
        echo "<pre>";
        var_dump('test 2');
        echo "</pre>";
        die;
    }

    public function filter_block_tree(array $block): array
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) return $block;

        // 1) Can thiệp attrs cho từng block cụ thể
        $block = $this->map_blocks($block, function (array $b) {
            $name = $b['blockName'] ?? '';

            // A) Actions block: tắt ReturnToCart + đổi label nút
            if ($name === 'woocommerce/checkout-actions-block') {
                $b['attrs']['showReturnToCart'] = false;                 // <- key
                $b['attrs']['placeOrderButtonLabel'] = __('Thanh toán ngay'); // <- label theo thiết kế
                $b['attrs']['returnToCartButtonLabel'] = __('Trở về giỏ hàng');
                $b['attrs']['className'] = trim(($b['attrs']['className'] ?? '') . ' tcp-checkout-actions');

                // echo "<pre>";
                // var_dump($b);
                // echo "</pre>";
                // die;
            }

            // B) Contact block: đổi title (nếu Woo hỗ trợ attrs title)
            if ($name === 'woocommerce/checkout-contact-information-block') {
                $b['attrs']['title'] = __('Thông tin người mua');
                $b['attrs']['className'] = trim(($b['attrs']['className'] ?? '') . ' tcp-checkout-contact');
            }
            if ($name === 'woocommerce/checkout-billing-address-block') {
                $b['attrs']['title'] = __('Thông tin người mua');
            }

            // C) Payment block: đổi title
            if ($name === 'woocommerce/checkout-payment-block') {
                $b['attrs']['title'] = __('Chọn phương thức thanh toán');
                $b['attrs']['className'] = trim(($b['attrs']['className'] ?? '') . ' tcp-checkout-payment');
            }

            // D) Totals/order summary: thêm class để style card (cột phải)
            if ($name === 'woocommerce/checkout-order-summary-block') {
                $b['attrs']['className'] = trim(($b['attrs']['className'] ?? '') . ' tcp-order-summary');
            }


            return $b;
        });

        // 2) (Tuỳ chọn) Reorder innerBlocks trong 2 cột nếu  muốn đổi vị trí section
        // Ví dụ: đưa Payment lên trước Billing/Shipping (tuỳ flow)
        $block = $this->reorder_checkout_fields($block);

        return $block;
    }

    private function map_blocks(array $block, callable $cb): array
    {
        $block = $cb($block);

        if (!empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $i => $child) {
                $block['innerBlocks'][$i] = $this->map_blocks($child, $cb);
            }
        }

        return $block;
    }

    private function reorder_checkout_fields(array $block): array
    {
        if (($block['blockName'] ?? '') !== 'woocommerce/checkout') return $block;
        if (empty($block['innerBlocks']) || !is_array($block['innerBlocks'])) return $block;

        // Tìm checkout-fields-block
        foreach ($block['innerBlocks'] as $colIndex => $col) {
            if (($col['blockName'] ?? '') !== 'woocommerce/checkout-fields-block') continue;

            $children = $col['innerBlocks'] ?? [];
            if (!is_array($children) || !$children) break;

            // Map blockName => block
            $byName = [];
            foreach ($children as $child) {
                $byName[$child['blockName'] ?? ''][] = $child;
            }

            // Tạo thứ tự mới theo ý  (bám theo list  export)
            $newOrder = [];
            $want = [
                'woocommerce/checkout-express-payment-block',
                'woocommerce/checkout-contact-information-block',
                'woocommerce/checkout-billing-address-block',
                'woocommerce/checkout-payment-block',
                'woocommerce/checkout-terms-block',
                'woocommerce/checkout-actions-block',
            ];

            // Add theo thứ tự mong muốn
            foreach ($want as $bn) {
                if (!empty($byName[$bn])) {
                    foreach ($byName[$bn] as $item) $newOrder[] = $item;
                    unset($byName[$bn]);
                }
            }

            // Append những block còn lại (giữ nguyên)
            foreach ($children as $child) {
                $bn = $child['blockName'] ?? '';
                if (in_array($bn, $want, true)) continue;
                $newOrder[] = $child;
            }

            $block['innerBlocks'][$colIndex]['innerBlocks'] = $newOrder;
            break;
        }

        return $block;
    }

    public function prepend_back_link(string $html, array $block): string
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) return $html;

        $cartUrl = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
        if (!$cartUrl) return $html;

        // Link ở đầu trang (theo design). Vì ta đã tắt ReturnToCart trong actions block.
        $back = sprintf(
            '<a href="%s" class="tcp-checkout-back__link button primary is-link"><i class="icon-arrow-left" aria-hidden="true"></i><span>%s</span></a>',
            esc_url($cartUrl),
            esc_html__('Trở về giỏ hàng', 'tcp-theme')
        );

        return $back . $html;
    }
}
