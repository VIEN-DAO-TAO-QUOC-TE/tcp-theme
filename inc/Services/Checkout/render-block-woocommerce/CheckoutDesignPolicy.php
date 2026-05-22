<?php

namespace TCP\Theme\Services\Checkout;

use TCP\Theme\Core\Singleton;
use WP_Error;

defined('ABSPATH') || exit;

final class CheckoutDesignPolicy
{
    use Singleton;

    private const EXT_NS = 'tcp_elearning';

    protected function init(): void
    {
        // 1) Ẩn field địa chỉ không cần (Blocks-friendly)
        add_filter('woocommerce_get_country_locale', [$this, 'filter_country_locale'], 20);

        // 2) Inject UI E-learning ngay trước payment block
        add_filter('render_block_woocommerce/checkout-payment-block', [$this, 'inject_elearning_section'], 10, 2);

        // 3) Enqueue JS chỉ ở checkout
        add_action('wp_enqueue_scripts', [$this, 'enqueue_checkout_script'], 30);

        // 4) Validate Store API checkout request
        add_filter('rest_authentication_errors', [$this, 'validate_store_api_checkout'], 20);

        // 5) Lưu meta vào order sau khi checkout tạo order (Store API)
        add_action('woocommerce_store_api_checkout_order_processed', [$this, 'save_order_meta'], 10, 2);
    }

    /**
     * Ẩn các field địa chỉ thừa cho toàn bộ locale (mọi quốc gia).
     * Giữ address_1 (optional) + phone (required).
     */
    public function filter_country_locale(array $locale): array
    {
        $hide = ['company', 'address_2', 'postcode', 'city', 'state'];

        foreach ($locale as $country => $fields) {
            // hide thừa
            foreach ($hide as $key) {
                if (isset($locale[$country][$key])) {
                    $locale[$country][$key]['required'] = false;
                    $locale[$country][$key]['hidden']   = true;
                }
            }

            // address_1 optional theo thiết kế
            if (isset($locale[$country]['address_1'])) {
                $locale[$country]['address_1']['required'] = false;
                $locale[$country]['address_1']['hidden']   = false;
            }

            // phone required theo thiết kế (nếu locale có)
            if (isset($locale[$country]['phone'])) {
                $locale[$country]['phone']['required'] = true;
                $locale[$country]['phone']['hidden']   = false;
            }
        }

        return $locale;
    }

    /**
     * Inject section “Tài khoản TCP E-learning” trước Payment block.
     */
    public function inject_elearning_section(string $html, array $block): string
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) return $html;
        if (function_exists('is_order_received_page') && is_order_received_page()) return $html;

        $section = '
        <section class="tcp-elearn" data-tcp-elearn>
            <h3 class="tcp-elearn__title">Tài khoản TCP E-learning</h3>
            <p class="tcp-elearn__desc">Tài khoản khóa học của Talent Connect+ sẽ được tạo và cung cấp qua email bên dưới.</p>

            <div class="tcp-elearn__row">
                <label class="tcp-elearn__label" for="tcp-elearn-email">Email tài khoản E-learning</label>
                <div class="tcp-elearn__same">
                    <label class="tcp-elearn__same-label">
                        <input type="checkbox" data-tcp-elearn-same />
                        <span>Tương tự email người mua</span>
                    </label>
                </div>
            </div>

            <input
                id="tcp-elearn-email"
                class="tcp-elearn__input"
                type="email"
                placeholder="Nhập email"
                autocomplete="email"
                data-tcp-elearn-email
            />

            <p class="tcp-elearn__error" data-tcp-elearn-error style="display:none;"></p>
        </section>
        ';

        return $section . $html;
    }

    public function enqueue_checkout_script(): void
    {
        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) return;
        if (function_exists('is_order_received_page') && is_order_received_page()) return;

        $rel  = 'dist/js/woo/checkout-elearning.js';
        $path = TCP_THEME_PATH . $rel;
        $url  = TCP_THEME_URI . $rel;

        wp_enqueue_script(
            'tcp-checkout-elearning',
            $url,
            ['wp-data'],
            file_exists($path) ? (string) filemtime($path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
            true
        );

        wp_add_inline_script(
            'tcp-checkout-elearning',
            'window.TCP_ELEARNING = ' . wp_json_encode([
                'ns' => self::EXT_NS,
                'messages' => [
                    'required' => 'Vui lòng nhập email tài khoản E-learning.',
                    'invalid'   => 'Email không hợp lệ.',
                ],
            ]) . ';',
            'before'
        );
    }

    public function validate_store_api_checkout($result)
    {
        if (is_wp_error($result)) return $result;

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
        if ($method !== 'POST') return $result;

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!preg_match('#/wc/store/v\d+/checkout#i', $uri)) return $result;

        $raw = file_get_contents('php://input');
        $body = $raw ? json_decode($raw, true) : null;
        if (!is_array($body)) return $result;

        $email = $body['extensions'][ self::EXT_NS ]['email'] ?? '';
        $email = is_string($email) ? trim($email) : '';

        if ($email === '') {
            return new WP_Error('tcp_elearn_missing', 'Missing e-learning email.', ['status' => 400]);
        }
        if (!is_email($email)) {
            return new WP_Error('tcp_elearn_invalid', 'Invalid e-learning email.', ['status' => 400]);
        }

        return $result;
    }

    /**
     * Lưu meta vào order (để admin xem lại / dùng tạo account).
     */
    public function save_order_meta($order, $request): void
    {
        if (!is_object($order) || !method_exists($order, 'update_meta_data')) return;

        $extensions = $request['extensions'] ?? [];
        $email = $extensions[self::EXT_NS]['email'] ?? '';
        $email = is_string($email) ? trim($email) : '';

        if ($email && is_email($email)) {
            $order->update_meta_data('_tcp_elearning_email', $email);
            $order->save();
        }
    }
}
