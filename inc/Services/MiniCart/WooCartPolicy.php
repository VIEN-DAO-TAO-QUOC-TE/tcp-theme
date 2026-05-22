<?php

namespace TCP\Theme\Services\MiniCart;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class WooCartPolicy
{
    use Singleton;

    protected function init(): void
    {
        // 1) Chặn truy cập trực tiếp trang giỏ hàng
        add_action('template_redirect', [$this, 'maybe_redirect_cart_page'], 20);

        // 2) Noindex trang giỏ hàng
        add_filter('wp_robots', [$this, 'noindex_cart_page']);

        // 3) (Tuỳ chọn) Đổi mọi cart url -> checkout
        // add_filter('woocommerce_get_cart_url', [$this, 'force_cart_url_to_checkout'], 20);
    }

    public function maybe_redirect_cart_page(): void
    {
        if (is_admin()) return;

        // tránh phá WC/AJAX fragments + các request kỹ thuật
        if (wp_doing_ajax()) return;
        if (defined('REST_REQUEST') && REST_REQUEST) return;

        if (!function_exists('is_cart') || !is_cart()) return;

        // Nếu có tham số đặc biệt để debug thì có thể cho phép bypass
        // if (isset($_GET['debug_cart'])) return;

        // Redirect sang checkout
        $checkout = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/thanh-toan/');
        wp_safe_redirect($checkout, 302);
        exit;
    }

    public function noindex_cart_page(array $robots): array
    {
        if (function_exists('is_cart') && is_cart()) {
            $robots['noindex'] = true;
            $robots['nofollow'] = true;
        }
        return $robots;
    }

    public function force_cart_url_to_checkout(string $url): string
    {
        return function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : $url;
    }
}
