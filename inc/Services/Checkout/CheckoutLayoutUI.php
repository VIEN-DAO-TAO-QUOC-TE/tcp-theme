<?php

namespace TCP\Theme\Services\Checkout;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Dev\HookDebugger;

defined('ABSPATH') || exit;

final class CheckoutLayoutUI
{
    use Singleton;

    protected function init(): void
    {
        // HookDebugger::boot([
        //     'enabled'    => true,
        //     'hooks'      => [
        //         'woocommerce_after_shop_loop_item_title',
        //     ],
        //     'only_admin' => true,
        //     'position'   => 'br',
        //     'when'       => function () {
        //         return is_checkout();
        //     },
        // ]);

        // add_action('init', [$this, 'register']);
        // add_action('after_setup_theme', [$this, 'register']);
        
        add_action('wp_loaded', [$this, 'register']);

        add_action('woocommerce_checkout_payment', 'woocommerce_checkout_payment', 20);

        add_filter('woocommerce_checkout_fields', [$this, 'custom_override_checkout_fields']);

        add_action('woocommerce_checkout_before_customer_details', [$this, 'prepend_back_link'], 10);

        add_filter('woocommerce_enable_order_notes_field', '__return_false');

        add_filter('woocommerce_form_field', [$this, 'filter_form_field_html'], 20, 4);

        add_action('woocommerce_checkout_create_order', [$this, 'save_tcp_fields'], 20, 2);
        add_filter('woocommerce_form_field', [$this, 'tweak_elearing_field_markup'], 20, 4);
    }
    public function register()
    {
        remove_action('woocommerce_checkout_after_order_review', 'wc_checkout_privacy_policy_text');
        remove_action('woocommerce_checkout_after_order_review', 'wc_checkout_privacy_policy_text', 1);
        remove_action('woocommerce_checkout_after_order_review', 'flatsome_html_checkout_sidebar', 10);
    }

    public function custom_override_checkout_fields($fields)
    {
        // Không dùng shipping
        unset($fields['shipping']);

        // Bỏ last_name vì UI có 1 ô "Họ và tên"
        unset($fields['billing']['billing_last_name']);

        // Bỏ các field không dùng
        unset($fields['billing']['billing_country']);
        unset($fields['billing']['billing_address_2']);
        unset($fields['billing']['billing_postcode']);
        unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_state']);

        // --- Customize theo design ---
        // Họ và tên
        $fields['billing']['billing_first_name']['label'] = __('Họ và tên', 'tcp-theme');
        $fields['billing']['billing_first_name']['required'] = true;
        $fields['billing']['billing_first_name']['priority'] = 10;
        $fields['billing']['billing_first_name']['class'] = ['form-row-wide', 'tcp-wc-field', 'mb-4'];
        $fields['billing']['billing_first_name']['input_class'] = ['input'];
        $fields['billing']['billing_first_name']['placeholder'] = __('Nhập họ và tên', 'tcp-theme');

        // Email
        $fields['billing']['billing_email']['label'] = __('Email', 'tcp-theme');
        $fields['billing']['billing_email']['required'] = true;
        $fields['billing']['billing_email']['priority'] = 20;
        $fields['billing']['billing_email']['class'] = ['form-row-wide', 'tcp-wc-field', 'mb-4'];
        $fields['billing']['billing_email']['input_class'] = ['input'];
        $fields['billing']['billing_email']['placeholder'] = __('Nhập email của ', 'tcp-theme');

        // Phone (optional)
        $fields['billing']['billing_phone']['label'] = __('Số điện thoại', 'tcp-theme');
        $fields['billing']['billing_phone']['required'] = false;
        $fields['billing']['billing_phone']['priority'] = 30;
        $fields['billing']['billing_phone']['class'] = ['form-row-wide', 'tcp-wc-field', 'mb-4'];
        $fields['billing']['billing_phone']['input_class'] = ['input'];
        $fields['billing']['billing_phone']['placeholder'] = __('Nhập số điện thoại của ', 'tcp-theme');

        // Address (optional theo design)
        $fields['billing']['billing_address_1']['label'] = __('Địa chỉ', 'tcp-theme');
        $fields['billing']['billing_address_1']['required'] = false;
        $fields['billing']['billing_address_1']['priority'] = 40;
        $fields['billing']['billing_address_1']['class'] = ['form-row-wide', 'tcp-wc-field', 'mb-4'];
        $fields['billing']['billing_address_1']['input_class'] = ['input'];
        $fields['billing']['billing_address_1']['placeholder'] = __('Nhập địa chỉ của  (không bắt buộc)', 'tcp-theme');

        // ===== Custom E-learning fields (group riêng) =====
        // Lưu ý: Woo sẽ render group "billing" thôi nếu  loop billing fields.
        // Cách gọn: add vào billing fields nhưng prefix tcp_ để phân biệt.

        $fields['billing']['tcp_elearing_email'] = [
            'type'        => 'email',
            'label'       => __('Email tài khoản E-learning', 'tcp-theme'),
            'required'    => false,
            'priority'    => 60,
            'class'       => ['form-row-wide', 'tcp-wc-field', 'mb-4'],
            'input_class' => ['input'],
            'placeholder' => __('Nhập email', 'tcp-theme'),
        ];

        $fields['billing']['tcp_elearing_same_as_buyer'] = [
            'type'        => 'checkbox',
            'label'       => __('Tương tự email người mua', 'tcp-theme'),
            'required'    => false,
            'priority'    => 61,
            'class'       => ['form-row-wide', 'tcp-wc-field', 'mb-4'],
        ];

        return $fields;
    }

    public function filter_form_field_html($field, $key, $args, $value)
    {
        // Chỉ áp dụng checkout
        if (!is_checkout()) return $field;

        // Thêm wrapper class chuẩn BEM
        // (Woo đã tạo <p class="form-row ..."> rồi, ta chỉ thêm class/markup nhỏ)
        // Ví dụ: thêm class cho label, input wrapper...

        // 1) Label class
        if (!empty($args['label_class'])) {
            $args['label_class'][] = 'tcp-field__label';
        }

        // 2) Input class
        if (!empty($args['input_class'])) {
            $args['input_class'][] = 'tcp-field__input';
        }

        // 3) Optional text theo design: không dùng "(tuỳ chọn)" mặc định (nếu  muốn bỏ)
        // Woo tự render optional. Ta có thể replace:
        // $field = str_replace('<span class="optional">', '<span class="optional tcp-field__optional">', $field);

        return $field;
    }

    public function save_tcp_fields($order, $data)
    {
        if (isset($_POST['tcp_elearing_email'])) {
            $order->update_meta_data('_tcp_elearing_email', sanitize_email(wp_unslash($_POST['tcp_elearing_email'])));
        }

        $same = !empty($_POST['tcp_elearing_same_as_buyer']) ? 'yes' : 'no';
        $order->update_meta_data('_tcp_elearing_same_as_buyer', $same);

        // Nếu same_as_buyer => tự set email elearning = billing_email (server-side cho chắc)
        if ($same === 'yes' && !empty($data['billing_email'])) {
            $order->update_meta_data('_tcp_elearing_email', sanitize_email($data['billing_email']));
        }
    }

    public function prepend_back_link()
    {
        $html = '';

        if (is_admin() || !function_exists('is_checkout') || !is_checkout()) return $html;

        $cartUrl = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
        if (!$cartUrl) return $html;

        $html = sprintf(
            '<a href="%s" class="tcp-checkout-back__link button primary is-link"><i class="icon-arrow-left" aria-hidden="true"></i><span>%s</span></a>',
            esc_url($cartUrl),
            esc_html__('Trở về giỏ hàng', 'tcp-theme')
        );
        echo $html;
    }

    public function tweak_elearing_field_markup($field, $key, $args, $value)
    {
        if (!is_checkout()) return $field;

        if ($key === 'tcp_elearing_email') {
            // Ẩn label của email field (vì đã có title ở header row)
            $field = preg_replace('/<label[^>]*>.*?<\/label>/s', '', $field, 1);
            // Ensure wrapper full width & spacing theo design
            $field = str_replace('form-row', 'form-row form-row-wide', $field);
            return $field;
        }

        if ($key === 'tcp_elearing_same_as_buyer') {
            // Checkbox: giữ label text, nhưng bỏ wrapper <p> margins để nằm gọn trên 1 hàng
            // (Tuỳ CSS  sẽ style. Ở đây chỉ thêm class hook)
            $field = preg_replace('/class="([^"]*)"/', 'class="$1 c-elearning-toggle"', $field, 1);
            return $field;
        }

        return $field;
    }
}
