<?php

namespace TCP\Theme\Services\Checkout;

use TCP\Theme\Core\Singleton;
use WP_Error;

defined('ABSPATH') || exit;

final class CheckoutProtection
{
    use Singleton;

    /** Namespace gửi lên Store API: extensions[NS] */
    private const EXT_NS = 'tcp_protection';

    protected function init(): void
    {
        // 1) Inject UI vào đúng block "actions" (chỗ nút Place order)
        add_filter('render_block_woocommerce/checkout-actions-block', [$this, 'inject_protection_ui'], 10, 2);

        // 2) Enqueue JS để setExtensionData
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 20);

        // 3) Validate sớm khi checkout gọi Store API endpoint
        add_filter('rest_authentication_errors', [$this, 'validate_store_api_checkout'], 20);
    }

    /**
     * Inject UI (HTML) ngay trước nút Place order.
     * Lưu ý: actions-block server-side có thể là placeholder, nhưng prepend markup ở đây vẫn là cách Woo hướng dẫn.
     */
    public function inject_protection_ui(string $html, array $block): string
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) {
            return $html;
        }
        // Không hiện ở trang order received
        if (function_exists('is_order_received_page') && is_order_received_page()) {
            return $html;
        }

        // Ví dụ UI: checkbox “Tôi xác nhận…”
        //  có thể thay bằng captcha widget / input token...
        $ui = '
        <div class="tcp-protection" data-tcp-protection>
            <label class="tcp-protection__row">
                <input type="checkbox" class="tcp-protection__checkbox" data-tcp-protection-check />
                <span class="tcp-protection__text">Tôi xác nhận thông tin thanh toán là chính xác.</span>
            </label>
            <p class="tcp-protection__hint" data-tcp-protection-hint style="display:none;"></p>
        </div>';

        // Đặt UI lên đầu của actions block (trước nút)
        return $ui . $html;
    }

    public function enqueue_scripts(): void
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) {
            return;
        }
        if (function_exists('is_order_received_page') && is_order_received_page()) {
            return;
        }

        // Script của 
        $handle = 'tcp-checkout-protection';
        $src    = get_stylesheet_directory_uri() . '/assets/js/tcp-checkout-protection.js';
        $ver    = defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : '1.0.0';

        // Phụ thuộc: wp-data (để dispatch), wp-i18n optional
        wp_enqueue_script($handle, $src, ['wp-data'], $ver, true);

        // Pass config sang JS
        wp_add_inline_script(
            $handle,
            'window.TCP_CHECKOUT_PROTECTION = ' . wp_json_encode([
                'ns' => self::EXT_NS,
                'messages' => [
                    'required' => 'Vui lòng xác nhận trước khi thanh toán.',
                ],
            ]) . ';',
            'before'
        );
    }

    /**
     * Validate sớm trên Store API checkout endpoint.
     * Chặn ngay từ lớp auth để không tốn xử lý tiếp.
     */
    public function validate_store_api_checkout($result)
    {
        // Nếu có error sẵn từ nơi khác -> giữ nguyên
        if (is_wp_error($result)) {
            return $result;
        }

        // Chỉ check request POST
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        if (strtoupper($method) !== 'POST') {
            return $result;
        }

        // Chỉ check đúng endpoint Store API checkout
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!$this->is_store_api_checkout_route($uri)) {
            return $result;
        }

        // Lấy JSON body
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return new WP_Error('tcp_protection_missing', 'Missing request body.', ['status' => 400]);
        }

        $body = json_decode($raw, true);
        if (!is_array($body)) {
            return new WP_Error('tcp_protection_invalid', 'Invalid JSON body.', ['status' => 400]);
        }

        // extensions[tcp_protection][confirmed] phải true
        $confirmed = $body['extensions'][self::EXT_NS]['confirmed'] ?? null;

        if ($confirmed !== true) {
            return new WP_Error(
                'tcp_protection_failed',
                'Vui lòng xác nhận trước khi thanh toán.',
                ['status' => 400]
            );
        }

        return $result;
    }

    private function is_store_api_checkout_route(string $uri): bool
    {
        // Pattern thường gặp:
        // /wp-json/wc/store/v1/checkout
        // /wp-json/wc/store/v2/checkout
        // hoặc có query string
        // Mình match "wc/store" + "/checkout"
        if (stripos($uri, '/wc/store/') === false) {
            return false;
        }
        if (preg_match('#/wc/store/v\d+/checkout#i', $uri)) {
            return true;
        }
        // fallback nếu không có v
        return (stripos($uri, '/wc/store/checkout') !== false);
    }
}
