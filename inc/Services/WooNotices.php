<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class WooNotices
{
    use Singleton;

    /**
     * Danh sách product ID chỉ được mua tối đa 1 cái trong cart.
     */
    private array $limit_one_product_ids = [
        // 123,
    ];

    public function register(): void
    {
        // 1) Chặn khi add-to-cart (hiện notice đúng thời điểm user bấm add lần 2)
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_add_to_cart_limit_one'], 10, 3);

        // 2) Chặn khi update cart (user tăng qty lên 2 ở cart)
        add_filter('woocommerce_update_cart_validation', [$this, 'validate_update_cart_limit_one'], 10, 4);

        // 3) Nếu có nơi nào vẫn cứ wc_add_notice mỗi lần load → chặn duplicate notice theo session
        // add_filter('woocommerce_add_notice', [$this, 'prevent_duplicate_notice'], 10, 2);
    }

    /**
     * Helper: set ids ở nơi khác nếu muốn cấu hình qua theme option.
     */
    public function set_limit_one_product_ids(array $ids): void
    {
        $this->limit_one_product_ids = array_values(array_unique(array_map('intval', $ids)));
    }

    private function is_limited_product(int $product_id): bool
    {
        return in_array($product_id, $this->limit_one_product_ids, true);
    }

    private function get_cart_qty_for_product(int $product_id): int
    {
        if (!WC()->cart) return 0;

        $qty = 0;
        foreach (WC()->cart->get_cart() as $item) {
            if ((int)($item['product_id'] ?? 0) === $product_id) {
                $qty += (int)($item['quantity'] ?? 0);
            }
        }
        return $qty;
    }

    public function validate_add_to_cart_limit_one(bool $passed, int $product_id, int $quantity): bool
    {
        if (!$passed) return false;
        if (!$this->is_limited_product($product_id)) return $passed;
        if (!WC()->cart) return $passed;

        $in_cart = $this->get_cart_qty_for_product($product_id);

        if ($in_cart + $quantity > 1) {
            wc_add_notice(__('Sản phẩm này chỉ được mua tối đa 1 lần trong giỏ hàng.', 'tcp'), 'error');
            return false;
        }

        return $passed;
    }

    public function validate_update_cart_limit_one(bool $passed, string $cart_item_key, array $values, int $quantity): bool
    {
        if (!$passed) return false;

        $product_id = (int)($values['product_id'] ?? 0);
        if (!$product_id) return $passed;

        if (!$this->is_limited_product($product_id)) return $passed;

        if ($quantity > 1) {
            wc_add_notice(__('Sản phẩm này chỉ được mua tối đa 1 lần trong giỏ hàng.', 'tcp'), 'error');
            return false;
        }

        return $passed;
    }

    /**
     * Chặn notice bị add lặp lại (cùng message + type) theo session.
     * => Reload trang sẽ không bị hiện lại nếu ai đó đang gọi wc_add_notice trên mỗi request.
     *
     * Lưu ý: Nếu message thay đổi (ví dụ có tên sản phẩm khác), nó vẫn add bình thường.
     */
    public function prevent_duplicate_notice(string $message, string $notice_type): string
    {
        // Chỉ chạy frontend
        if (is_admin()) return $message;

        // Woo session có thể chưa sẵn ở vài thời điểm rất sớm
        if (!function_exists('WC') || !WC() || !WC()->session) return $message;

        // Nếu message rỗng thì thôi
        $message = (string) $message;
        if ($message === '') return $message;

        $key = 'tcp_notice_once_' . md5($notice_type . '|' . wp_strip_all_tags($message));
        $already = WC()->session->get($key);

        if ($already) {
            // Trả về chuỗi rỗng để Woo không add notice này nữa
            return '';
        }

        WC()->session->set($key, 1);
        return $message;
    }
}
