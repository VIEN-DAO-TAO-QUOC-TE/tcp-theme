<?php

namespace TCP\Theme\Services\Checkout;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CheckoutCouponBeforePayment
{
    use Singleton;

    protected function init(): void
    {
        add_action('wp', [$this, 'setup'], 20);

        add_action('wp_ajax_apply_checkout_coupon', [$this, 'ajaxApplyCoupon']);
        add_action('wp_ajax_nopriv_apply_checkout_coupon', [$this, 'ajaxApplyCoupon']);
    }

    public function setup(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        // 1) Ẩn toggle mặc định (để không hiện "Have a coupon? Click here..." của Woo)
        add_action('woocommerce_before_checkout_form', [$this, 'printHideDefaultToggleCss'], 5);

        // 2) Remove coupon form mặc định hoàn toàn
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

        // 3) Render coupon form custom ngay trước payment
        remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
        add_action('woocommerce_checkout_order_review', [$this, 'renderCustomCouponForm'], 30);
        add_action('woocommerce_checkout_order_review', [$this, 'woocommerce_order_button_html'], 30);

        // 4) In JS ở footer
        add_action('wp_footer', [$this, 'printFooterScript'], 30);
    }
    public function woocommerce_order_button_html()
    {
        $order_button_text = 'Đặt hàng';
        echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button primary alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine 
    }

    private function shouldRun(): bool
    {
        if (!function_exists('is_checkout') || !is_checkout()) return false;
        if (function_exists('is_order_received_page') && is_order_received_page()) return false;

        // is_wc_endpoint_url() true cho order-received, view-order... nhưng check thêm cho chắc
        if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()) return false;

        if (!function_exists('wc_coupons_enabled') || !wc_coupons_enabled()) return false;

        return true;
    }

    public function printHideDefaultToggleCss(): void
    {
        // Classic checkout toggle của Woo (woocommerce-form-coupon-toggle)
        echo '<style>.woocommerce-form-coupon-toggle{display:none!important;}</style>';
    }

    public function renderCustomCouponForm(): void
    {
        $nonce = wp_create_nonce('tcp_apply_checkout_coupon');

        echo '<div class="tcp-checkout-coupon">';
        echo '  <div class="coupon tcp-coupon" data-nonce="' . esc_attr($nonce) . '">
                <div class="flex-row medium-flex-wrap gap-half tcp-coupon__row">
                    <div class="icon-ticket-percent"></div>

                    <div class="flex-col flex-grow">
                        <label for="coupon_code" class="screen-reader-text">' . esc_html__('Ưu đãi:', 'tcp-theme') . '</label>
                        <input
                            type="text"
                            name="coupon_code"
                            class="input-text"
                            placeholder="' . esc_attr__('Nhập mã giảm giá', 'tcp-theme') . '"
                            id="coupon_code"
                            value=""
                            autocomplete="off"
                        >
                    </div>

                    <div class="flex-col">
                        <button type="submit" class="button expand" name="apply_coupon" value="' . esc_attr__('Áp dụng', 'tcp-theme') . '">'
            . esc_html__('Áp dụng', 'tcp-theme') .
            '</button>
                    </div>
                </div>
            </div>';
        echo '</div>';
    }

    public function printFooterScript(): void
    {
        if (!$this->shouldRun()) return;
?>
        <script type="text/javascript">
            jQuery(function($) {
                if (typeof wc_checkout_params === 'undefined') return;

                var $checkoutForm = $('form.checkout');
                if (!$checkoutForm.length) return;

                function getCouponEls() {
                    var $wrap = $checkoutForm.find('.tcp-coupon').first();
                    return {
                        $wrap: $wrap,
                        $input: $wrap.find('input[name="coupon_code"]').first(),
                        $btn: $wrap.find('button[name="apply_coupon"]').first(),
                        nonce: $wrap.data('nonce') || ''
                    };
                }

                function canApply(code) {
                    return (code || '').toString().trim().length >= 3;
                }

                function setButtonState($btn, enabled) {
                    if (!$btn.length) return;
                    $btn.prop('disabled', !enabled);
                    $btn.toggleClass('is-disabled', !enabled);
                    if (!enabled) $btn.attr('aria-disabled', 'true');
                    else $btn.removeAttr('aria-disabled');
                }

                function setLoading($btn, loading) {
                    if (!$btn.length) return;

                    var originalText = $btn.data('tcp-text');
                    if (!originalText) {
                        originalText = $.trim($btn.text());
                        $btn.data('tcp-text', originalText);
                    }

                    if (loading) {
                        $btn.data('tcp-loading', true)
                            .prop('disabled', true)
                            .addClass('is-loading')
                            .attr('aria-busy', 'true')
                            .text('Đang áp dụng...');
                    } else {
                        $btn.data('tcp-loading', false)
                            .removeClass('is-loading')
                            .removeAttr('aria-busy')
                            .text(originalText);

                        // sau khi xong loading, set disabled theo điều kiện input hiện tại
                        var codeNow = ($checkoutForm.find('.tcp-coupon input[name="coupon_code"]').val() || '');
                        setButtonState($btn, canApply(codeNow));
                    }
                }

                // Init: disable nếu chưa đủ ký tự
                (function initCoupon() {
                    var els = getCouponEls();
                    if (!els.$wrap.length) return;
                    setButtonState(els.$btn, canApply(els.$input.val()));
                })();

                // On typing: enable khi >= 3 ký tự
                $checkoutForm.on('input', '.tcp-coupon input[name="coupon_code"]', function() {
                    var $input = $(this);
                    var $btn = $input.closest('.tcp-coupon').find('button[name="apply_coupon"]');
                    if ($btn.data('tcp-loading')) return; // đang loading thì không đổi state
                    setButtonState($btn, canApply($input.val()));
                });

                // Chặn submit checkout khi click "Áp dụng" (button type="submit")
                $checkoutForm.on('click', '.tcp-coupon button[name="apply_coupon"]', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var $btn = $(this);
                    var $wrap = $btn.closest('.tcp-coupon');
                    var nonce = $wrap.data('nonce') || '';
                    var $input = $wrap.find('input[name="coupon_code"]');
                    var code = ($input.val() || '').toString().trim();

                    if (!canApply(code)) {
                        setButtonState($btn, false);
                        return;
                    }

                    if ($btn.data('tcp-loading')) return;

                    setLoading($btn, true);

                    $.ajax({
                        type: 'POST',
                        url: wc_checkout_params.ajax_url,
                        data: {
                            action: 'apply_checkout_coupon',
                            coupon_code: code,
                            security: nonce
                        },
                        success: function(html) {
                            $('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
                            $checkoutForm.before(html);
                            $(document.body).trigger('update_checkout');
                        },
                        error: function() {
                            // fallback notice (nếu server không trả html notice)
                            $('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
                            $checkoutForm.before(
                                '<div class="woocommerce-error" role="alert">Không thể áp dụng mã giảm giá. Vui lòng thử lại.</div>'
                            );
                        },
                        complete: function() {
                            setLoading($btn, false);
                        }
                    });
                });

                // Enter trong input -> trigger click (không submit checkout)
                $checkoutForm.on('keydown', '.tcp-coupon input[name="coupon_code"]', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $(this).closest('.tcp-coupon').find('button[name="apply_coupon"]').trigger('click');
                    }
                });
            });
        </script>
<?php
    }



    public function ajaxApplyCoupon(): void
    {
        // security
        check_ajax_referer('tcp_apply_checkout_coupon', 'security');

        if (!function_exists('WC') || !WC()->cart) {
            wp_die();
        }

        $code = isset($_POST['coupon_code']) ? (string) wp_unslash($_POST['coupon_code']) : '';
        $code = wc_format_coupon_code($code);

        if ($code !== '') {
            WC()->cart->add_discount($code);
        } else {
            if (class_exists('WC_Coupon')) {
                wc_add_notice(\WC_Coupon::get_generic_coupon_error(\WC_Coupon::E_WC_COUPON_PLEASE_ENTER), 'error');
            } else {
                wc_add_notice(__('Please enter a coupon code.', 'woocommerce'), 'error');
            }
        }

        wc_print_notices();
        wp_die();
    }
}
