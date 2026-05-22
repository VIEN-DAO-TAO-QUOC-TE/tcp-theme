<?php

namespace TCP\Theme\Services\MiniCart;

use TCP\Theme\Core\Singleton;
use WC_Coupon;
use WC_Product;

defined('ABSPATH') || exit;

final class MiniCartTotalsUI
{
    use Singleton;

    /**
     * Chỉ show gợi ý coupon nếu coupon có meta này = "yes"
     * (để tránh lộ coupon nội bộ)
     */
    private const COUPON_PUBLIC_META_KEY = '_tcp_show_in_mini_cart';

    /**
     * Nonce action cho coupon UI (copy/remove)
     */
    private const NONCE_ACTION = 'tcp_mini_cart_coupon_action';

    /**
     * Ajax action remove coupon
     */
    private const AJAX_REMOVE_ACTION = 'tcp_mini_cart_remove_coupon';

    protected function init(): void
    {
        // Đợi WP + plugins (Woo) load xong rồi mới remove/add
        add_action('init', [$this, 'register_totals_hooks'], 999);

        /**
         * Render block coupon hint trong mini cart (Flatsome hook)
         */
        add_action('flatsome_after_mini_cart_contents', [$this, 'render_coupon_hint_block'], 12);

        /**
         * Đảm bảo mini-cart fragments refresh khi coupon thay đổi
         */
        add_filter('woocommerce_cart_hash', function ($hash, $cart_session) {
            if (! function_exists('WC') || ! WC()->cart) {
                return $hash;
            }

            $coupons = WC()->cart->get_applied_coupons();
            if (empty($coupons)) {
                return $hash;
            }

            $coupons = array_map('wc_format_coupon_code', $coupons);

            return md5($hash . '|' . implode(',', $coupons));
        }, 20, 2);

        /**
         * Ajax remove coupon (tích hợp trong class)
         */
        add_action('wp_ajax_' . self::AJAX_REMOVE_ACTION, [$this, 'ajax_remove_coupon']);
        add_action('wp_ajax_nopriv_' . self::AJAX_REMOVE_ACTION, [$this, 'ajax_remove_coupon']);

        /**
         * Footer script (copy + remove) dùng delegation (mini cart refresh bằng fragments)
         */
        add_action('wp_footer', [$this, 'print_coupon_hint_copy_script'], 99);
    }

    public function register_totals_hooks(): void
    {
        /**
         * Override block tổng tiền trong mini-cart footer:
         * hook "woocommerce_widget_shopping_cart_total"
         */

        // Gỡ subtotal mặc định của Woo
        remove_action('woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal', 10);

        // Gắn totals UI theo thiết kế
        add_action('woocommerce_widget_shopping_cart_total_custom', [$this, 'render_mini_cart_totals'], 10);
    }

    /**
     * Render theo thiết kế:
     * - Tạm tính: WC()->cart->get_cart_subtotal()
     * - Tiết kiệm: chênh lệch (regular - current) theo từng item (sale saving)
     */
    public function render_mini_cart_totals(): void
    {
        if (! function_exists('WC') || ! WC()->cart) return;

        $subtotal_html = WC()->cart->get_cart_subtotal();
        $sale_saving   = $this->get_cart_sale_saving_amount();

        echo '<div class="tcp-mini-cart-totals">';

        echo '<div class="tcp-mini-cart-totals__row tcp-mini-cart-totals__row--subtotal">';
        echo '<span class="tcp-mini-cart-totals__label">' . esc_html__('Tạm tính:', 'tcp-theme') . '</span>';
        echo '<span class="tcp-mini-cart-totals__value">' . $subtotal_html . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';

        if ($sale_saving > 0) {
            echo '<div class="tcp-mini-cart-totals__row tcp-mini-cart-totals__row--saving">';
            echo '<span class="tcp-mini-cart-totals__label">' . esc_html__('Tiết kiệm:', 'tcp-theme') . '</span>';
            echo '<span class="tcp-mini-cart-totals__value tcp-mini-cart-totals__value--saving">' . wc_price($sale_saving) . '</span>';
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Coupon UI:
     * - Loop qua danh sách coupon:
     *   + Coupon đang áp dụng (applied) luôn được show (để có nút xóa)
     *   + Coupon public (meta yes) show để gợi ý
     * - Coupon nào đã áp dụng => nút Xóa
     * - Coupon nào chưa áp dụng => nút Lấy mã (copy)
     */
    public function render_coupon_hint_block(): void
    {
        if (! function_exists('WC') || ! WC()->cart) return;

        // đảm bảo cart load + totals được tính trong request hiện tại
        WC()->cart->get_cart();
        WC()->cart->calculate_totals();

        $applied = array_map('wc_format_coupon_code', (array) WC()->cart->get_applied_coupons());

        // Lấy coupon public (để gợi ý), lấy nhiều hơn 1
        $public_coupons = $this->get_public_coupons_for_hint(10);

        // Build danh sách codes: applied trước, rồi public (unique)
        $codes = [];

        foreach ($applied as $c) {
            if ($c) $codes[$c] = true;
        }

        foreach ($public_coupons as $cp) {
            $c = wc_format_coupon_code($cp->get_code());
            if ($c) $codes[$c] = true;
        }

        if (empty($codes)) return;

        $nonce   = wp_create_nonce(self::NONCE_ACTION);
        $ajaxUrl = admin_url('admin-ajax.php');

        echo '<div class="tcp-mini-cart-coupon-hints" data-ajax-url="' . esc_attr($ajaxUrl) . '" data-nonce="' . esc_attr($nonce) . '">';

        foreach (array_keys($codes) as $code) {
            $is_applied = in_array($code, $applied, true);

            // Cố gắng lấy object coupon để format label (nếu coupon tồn tại)
            $coupon = null;
            try {
                $coupon = new WC_Coupon($code);
                // nếu coupon không tồn tại/không hợp lệ => WC_Coupon có thể trống dữ liệu
            } catch (\Throwable $e) {
                $coupon = null;
            }

            $code_upper = strtoupper($code);

            // Label ưu đãi (chỉ show khi coupon hợp lệ)
            $benefit = '';
            if ($coupon instanceof WC_Coupon && $coupon->get_id()) {
                $benefit = $this->format_coupon_benefit_label($coupon);
            }

            /**
             * HTML đồng bộ thiết kế pill:
             * icon - text - button
             */
            echo '<div class="tcp-coupon-hint" data-coupon="' . esc_attr($code_upper) . '">';
            echo '<div class="tcp-coupon-hint__group">';
            echo '  <div class="tcp-coupon-hint__icon icon-ticket-percent" aria-hidden="true"></div>';

            echo '  <div class="tcp-coupon-hint__text">';
            if ($is_applied) {
                // Đã áp dụng: vẫn show text dạng gợi ý nhưng nhấn mạnh "đang áp dụng"
                echo esc_html__('Đang áp dụng', 'tcp-theme') . ' '
                    . '<strong class="tcp-coupon-hint__code">' . esc_html($code_upper) . '</strong>';
            } else {
                echo esc_html__('Nhập', 'tcp-theme') . ' '
                    . '<strong class="tcp-coupon-hint__code">' . esc_html($code_upper) . '</strong>'
                    . ' ' . esc_html__('để nhận ưu đãi', 'tcp-theme');

                if ($benefit !== '') {
                    echo ' <strong class="tcp-coupon-hint__benefit">' . esc_html($benefit) . '</strong>';
                }
            }
            echo '  </div>';
            echo '  </div>';

            if ($is_applied) {
                echo '  <button type="button"'
                    . ' class="tcp-coupon-hint__btn tcp-coupon-hint__btn--remove js-tcp-remove-coupon"'
                    . ' data-coupon="' . esc_attr($code) . '"'
                    . ' data-text-default="' . esc_attr__('Xóa mã', 'tcp-theme') . '"'
                    . ' data-text-loading="' . esc_attr__('Đang xóa...', 'tcp-theme') . '"'
                    . ' aria-label="' . esc_attr__('Xóa mã giảm giá', 'tcp-theme') . '"'
                    . '>'
                    . esc_html__('Xóa mã', 'tcp-theme')
                    . '</button>';
            } else {
                echo '  <button type="button"'
                    . ' class="tcp-coupon-hint__btn js-tcp-copy-coupon"'
                    . ' data-coupon="' . esc_attr($code_upper) . '"'
                    . ' data-text-default="' . esc_attr__('Lấy mã', 'tcp-theme') . '"'
                    . ' data-text-loading="' . esc_attr__('Đang lấy...', 'tcp-theme') . '"'
                    . ' data-text-copied="' . esc_attr__('Đã copy', 'tcp-theme') . '"'
                    . ' aria-label="' . esc_attr__('Copy mã giảm giá', 'tcp-theme') . '"'
                    . '>'
                    . esc_html__('Lấy mã', 'tcp-theme')
                    . '</button>';
            }

            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * AJAX: remove coupon khỏi cart
     */
    public function ajax_remove_coupon(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'security');

        if (! function_exists('WC') || ! WC()->cart) {
            wp_send_json_error(['message' => 'Cart not available'], 400);
        }

        $code = isset($_POST['coupon_code']) ? wc_format_coupon_code(wp_unslash($_POST['coupon_code'])) : '';
        if (! $code) {
            wp_send_json_error(['message' => 'Missing coupon_code'], 400);
        }

        WC()->cart->remove_coupon($code);
        WC()->cart->calculate_totals();

        wp_send_json_success([
            'applied' => array_map('wc_format_coupon_code', (array) WC()->cart->get_applied_coupons()),
        ]);
    }

    /**
     * Tính "tiết kiệm" do sale:
     * sum(max(0, regular - current) * qty)
     */
    private function get_cart_sale_saving_amount(): float
    {
        if (! function_exists('WC') || ! WC()->cart) return 0.0;

        $saving = 0.0;

        foreach ((array) WC()->cart->get_cart() as $cart_item) {
            if (empty($cart_item['data']) || ! $cart_item['data'] instanceof WC_Product) continue;

            /** @var WC_Product $product */
            $product = $cart_item['data'];
            $qty     = max(1, (int) ($cart_item['quantity'] ?? 1));

            $regular = (float) $product->get_regular_price();
            $current = (float) $product->get_price();

            if ($regular <= 0 || $current <= 0 || $regular <= $current) continue;

            $saving += ($regular - $current) * $qty;
        }

        return (float) $saving;
    }

    /**
     * Lấy danh sách coupon "public" để gợi ý trong mini-cart.
     * Chỉ lấy coupon:
     * - publish
     * - meta _tcp_show_in_mini_cart = yes
     * - chưa hết hạn (nếu có)
     */
    private function get_public_coupons_for_hint(int $limit = 10): array
    {
        $q = new \WP_Query([
            'post_type'      => 'shop_coupon',
            'post_status'    => 'publish',
            'posts_per_page' => max(1, $limit),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'     => self::COUPON_PUBLIC_META_KEY,
                    'value'   => 'yes',
                    'compare' => '=',
                ],
            ],
        ]);

        if (empty($q->posts)) return [];

        $out = [];
        $now = time();

        foreach ($q->posts as $coupon_id) {
            $coupon = new WC_Coupon((int) $coupon_id);

            $expiry = $coupon->get_date_expires();
            if ($expiry && $expiry->getTimestamp() < $now) continue;

            $out[] = $coupon;
        }

        return $out;
    }

    private function format_coupon_benefit_label(WC_Coupon $coupon): string
    {
        $type   = $coupon->get_discount_type();
        $amount = (float) $coupon->get_amount();

        if ($type === 'percent') {
            return rtrim(rtrim((string) $amount, '0'), '.') . '%';
        }

        return html_entity_decode(strip_tags(wc_price($amount)));
    }

    public function available_coupon_codes()
    {
        global $wpdb;

        $coupon_codes = $wpdb->get_col(
            "SELECT post_title FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_name ASC"
        );

        return implode(', ', (array) $coupon_codes);
    }

    /**
     * Footer Script:
     * - Copy coupon code
     * - Remove coupon (AJAX) + refresh mini cart fragments
     *
     * Lưu ý: dùng delegation để hoạt động cả khi mini cart refresh bằng AJAX/fragments.
     */
    public function print_coupon_hint_copy_script(): void
    {
        static $printed = false;
        if ($printed) return;
        $printed = true;
?>
        <script type="text/javascript">
            (function() {
                function copyText(text) {
                    if (!text) return Promise.reject(new Error('empty'));
                    if (navigator.clipboard && window.isSecureContext) {
                        return navigator.clipboard.writeText(text);
                    }
                    return new Promise(function(resolve, reject) {
                        try {
                            var ta = document.createElement('textarea');
                            ta.value = text;
                            ta.setAttribute('readonly', '');
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            ta.style.top = '-9999px';
                            document.body.appendChild(ta);
                            ta.select();
                            var ok = document.execCommand('copy');
                            document.body.removeChild(ta);
                            ok ? resolve() : reject(new Error('execCommand failed'));
                        } catch (e) {
                            reject(e);
                        }
                    });
                }

                function setBtnState(btn, state) {
                    var tDefault = btn.getAttribute('data-text-default') || btn.textContent || '';
                    var tLoading = btn.getAttribute('data-text-loading') || '...';
                    var tCopied = btn.getAttribute('data-text-copied') || 'Đã copy';

                    if (state === 'loading') {
                        btn.classList.add('is-loading');
                        btn.disabled = true;
                        btn.textContent = tLoading;
                        btn.setAttribute('aria-busy', 'true');
                        return;
                    }

                    if (state === 'copied') {
                        btn.classList.remove('is-loading');
                        btn.classList.add('is-copied');
                        btn.textContent = tCopied;
                        btn.removeAttribute('aria-busy');
                        return;
                    }

                    // idle
                    btn.classList.remove('is-loading', 'is-copied');
                    btn.disabled = false;
                    btn.textContent = btn.getAttribute('data-text-default') || tDefault;
                    btn.removeAttribute('aria-busy');
                }

                function getHintsRoot(el) {
                    return el.closest('.tcp-mini-cart-coupon-hints');
                }

                function ajaxRemoveCoupon(root, couponCode) {
                    var ajaxUrl = root ? root.getAttribute('data-ajax-url') : '';
                    var nonce = root ? root.getAttribute('data-nonce') : '';
                    if (!ajaxUrl || !nonce || !couponCode) return Promise.reject(new Error('missing'));

                    var fd = new FormData();
                    fd.append('action', '<?php echo esc_js(self::AJAX_REMOVE_ACTION); ?>');
                    fd.append('security', nonce);
                    fd.append('coupon_code', couponCode);

                    return fetch(ajaxUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: fd
                    }).then(function(r) {
                        return r.json();
                    });
                }

                // Delegation: mini-cart thường refresh fragments/AJAX
                document.addEventListener('click', function(e) {

                    // COPY
                    var copyBtn = e.target.closest('.js-tcp-copy-coupon');
                    if (copyBtn) {
                        e.preventDefault();

                        if (copyBtn.dataset.tcpLoading === '1') return;

                        var code = (copyBtn.getAttribute('data-coupon') || '').trim();
                        if (!code) return;

                        copyBtn.dataset.tcpLoading = '1';
                        setBtnState(copyBtn, 'loading');

                        copyText(code).then(function() {
                            setBtnState(copyBtn, 'copied');
                            window.setTimeout(function() {
                                copyBtn.dataset.tcpLoading = '0';
                                setBtnState(copyBtn, 'idle');
                            }, 1200);
                        }).catch(function() {
                            copyBtn.dataset.tcpLoading = '0';
                            setBtnState(copyBtn, 'idle');
                        });

                        return;
                    }

                    // REMOVE
                    var removeBtn = e.target.closest('.js-tcp-remove-coupon');
                    if (removeBtn) {
                        e.preventDefault();

                        if (removeBtn.dataset.tcpLoading === '1') return;

                        var root = getHintsRoot(removeBtn);
                        var codeRemove = (removeBtn.getAttribute('data-coupon') || '').trim();
                        if (!codeRemove) return;

                        removeBtn.dataset.tcpLoading = '1';
                        setBtnState(removeBtn, 'loading');

                        ajaxRemoveCoupon(root, codeRemove).then(function(res) {
                            // Dù success hay fail, vẫn refresh fragments để UI sync đúng với cart
                            if (window.jQuery && window.jQuery(document.body)) {
                                window.jQuery(document.body).trigger('wc_fragment_refresh');
                                window.jQuery(document.body).trigger('update_checkout');
                            }
                        }).catch(function() {
                            // ignore
                        }).finally(function() {
                            removeBtn.dataset.tcpLoading = '0';
                            setBtnState(removeBtn, 'idle');

                            // refresh fragments lần nữa nếu không có jQuery fallback
                            if (!window.jQuery) {
                                try {
                                    document.dispatchEvent(new Event('wc_fragment_refresh'));
                                } catch (err) {}
                            }
                        });

                        return;
                    }
                });

            })();
        </script>
<?php
    }
}
