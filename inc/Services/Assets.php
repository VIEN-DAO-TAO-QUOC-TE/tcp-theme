<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class Assets
{
    use Singleton;

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
    }

    public function enqueue_frontend(): void
    {
        $variables_rel = 'assets/css/variables.css';
        $variables_path = TCP_THEME_PATH . $variables_rel;
        $variables_url = TCP_THEME_URI . $variables_rel;

        wp_enqueue_style(
            'tcp-theme-variables',
            $variables_url,
            ['flatsome-main'],
            file_exists($variables_path) ? (string) filemtime($variables_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
        );

        $css_path = TCP_THEME_PATH . 'dist/css/theme.css';
        $js_path  = TCP_THEME_PATH . 'dist/js/theme.js';

        // lucide font
        $rel  = 'assets/vendor/lucide-font/lucide.css';
        $path = TCP_THEME_PATH . $rel;
        $url  = TCP_THEME_URI . $rel;

        wp_enqueue_style(
            'tcp-font-inter',
            'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap',
            [],
            null
        );

        wp_enqueue_style('lucide-icon-font', $url, ['flatsome-main'], file_exists($path) ? filemtime($path) : null);

        wp_enqueue_style(
            'tcp-theme',
            TCP_THEME_URI . 'dist/css/theme.css',
            ['tcp-theme-variables', 'lucide-icon-font', 'flatsome-main', 'flatsome-shop'],
            file_exists($css_path) ? (string) filemtime($css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
        );

        $isCsherpaPage = (function_exists('is_page_template') && is_page_template('page-c-sherpa-next.php'))
            || (function_exists('is_page') && is_page(['c-sherpa-next', 'c-sherpa-reset', 'c-sherpa-transform']));

        if ($isCsherpaPage) {
            $page_css_rel  = 'assets/css/page-c-sherpa-next.css';
            $page_css_path = TCP_THEME_PATH . $page_css_rel;
            $page_css_url  = TCP_THEME_URI . $page_css_rel;

            wp_enqueue_style(
                'tcp-page-c-sherpa-next',
                $page_css_url,
                ['tcp-theme'],
                file_exists($page_css_path) ? (string) filemtime($page_css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
            );
        }

        if (function_exists('is_page_template') && is_page_template('page-xac-nhan-dang-ky.php')) {
            $confirm_css_rel  = 'assets/css/page-xac-nhan-dang-ky.css';
            $confirm_css_path = TCP_THEME_PATH . $confirm_css_rel;
            $confirm_css_url  = TCP_THEME_URI . $confirm_css_rel;

            wp_enqueue_style(
                'tcp-page-xac-nhan-dang-ky',
                $confirm_css_url,
                ['tcp-theme'],
                file_exists($confirm_css_path) ? (string) filemtime($confirm_css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
            );
        }

        if (function_exists('is_page_template') && is_page_template('page-doanh-nghiep.php')) {
            $business_css_rel  = 'assets/css/page-doanh-nghiep.css';
            $business_css_path = TCP_THEME_PATH . $business_css_rel;
            $business_css_url  = TCP_THEME_URI . $business_css_rel;

            wp_enqueue_style(
                'tcp-page-doanh-nghiep',
                $business_css_url,
                ['tcp-theme'],
                file_exists($business_css_path) ? (string) filemtime($business_css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
            );
        }

        if (is_singular('tcp_trainer')) {
            $trainer_css_rel  = 'assets/css/page-trainer-detail.css';
            $trainer_css_path = TCP_THEME_PATH . $trainer_css_rel;
            $trainer_css_url  = TCP_THEME_URI . $trainer_css_rel;

            wp_enqueue_style(
                'tcp-page-trainer-detail',
                $trainer_css_url,
                ['tcp-theme'],
                file_exists($trainer_css_path) ? (string) filemtime($trainer_css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
            );
        }

        if (is_singular('post')) {
            $single_post_css_rel  = 'assets/css/single-post.css';
            $single_post_css_path = TCP_THEME_PATH . $single_post_css_rel;
            $single_post_css_url  = TCP_THEME_URI . $single_post_css_rel;

            wp_enqueue_style(
                'tcp-single-post',
                $single_post_css_url,
                ['tcp-theme'],
                file_exists($single_post_css_path) ? (string) filemtime($single_post_css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
            );
        }

        if (is_search()) {
            $search_css_rel  = 'assets/css/search.css';
            $search_css_path = TCP_THEME_PATH . $search_css_rel;
            $search_css_url  = TCP_THEME_URI . $search_css_rel;

            wp_enqueue_style(
                'tcp-search',
                $search_css_url,
                ['tcp-theme'],
                file_exists($search_css_path) ? (string) filemtime($search_css_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null)
            );
        }

        /**
         * Fancybox (local)
         */
        if (is_product()) {
            $fb_css_rel  = 'assets/vendor/fancybox/fancybox.css';
            $fb_js_rel   = 'assets/vendor/fancybox/fancybox.umd.js';

            $fb_css_path = TCP_THEME_PATH . $fb_css_rel;
            $fb_js_path  = TCP_THEME_PATH . $fb_js_rel;

            $fb_css_url  = TCP_THEME_URI . $fb_css_rel;
            $fb_js_url   = TCP_THEME_URI . $fb_js_rel;

            wp_enqueue_style(
                'tcp-fancybox',
                $fb_css_url,
                ['tcp-theme'],
                file_exists($fb_css_path) ? (string) filemtime($fb_css_path) : null
            );

            wp_enqueue_script(
                'tcp-fancybox',
                $fb_js_url,
                [],
                file_exists($fb_js_path) ? (string) filemtime($fb_js_path) : null,
                true
            );
        }

        // Theme JS
        $deps = ['jquery', 'flatsome-js'];
        if (is_product()) $deps[] = 'tcp-fancybox';

        wp_enqueue_script(
            'tcp-theme',
            TCP_THEME_URI . 'dist/js/theme.js',
            $deps,
            file_exists($js_path) ? (string) filemtime($js_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
            true
        );

        /**
         * ==========================================
         * Woo - Cart page only
         * File: dist/js/woo/cart.js
         * ==========================================
         */
        if (function_exists('is_cart') && is_cart()) {
            $cart_rel  = 'dist/js/woo/cart.js';
            $cart_path = TCP_THEME_PATH . $cart_rel;
            $cart_url  = TCP_THEME_URI . $cart_rel;

            wp_enqueue_script(
                'tcp-woo-cart',
                $cart_url,
                ['jquery', 'flatsome-js', 'tcp-theme'],
                file_exists($cart_path) ? (string) filemtime($cart_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
                true
            );
        }

        /**
         * ==========================================
         * Woo - Checkout page only
         * File: dist/js/woo/checkout-protection.js
         * ==========================================
         */
        if (function_exists('is_checkout') && is_checkout() && !(function_exists('is_order_received_page') && is_order_received_page())) {

            /**
             * ============================
             * Checkout Protection
             * ============================
             */
            $ck_rel  = 'dist/js/woo/checkout-protection.js';
            $ck_path = TCP_THEME_PATH . $ck_rel;
            $ck_url  = TCP_THEME_URI . $ck_rel;

            wp_enqueue_script(
                'tcp-checkout-protection',
                $ck_url,
                ['wp-data', 'tcp-theme'],
                file_exists($ck_path) ? (string) filemtime($ck_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
                true
            );

            wp_add_inline_script(
                'tcp-checkout-protection',
                'window.TCP_CHECKOUT_PROTECTION = ' . wp_json_encode([
                    'ns' => 'tcp_protection',
                    'messages' => [
                        'required' => 'Vui lòng xác nhận trước khi thanh toán.',
                    ],
                ]) . ';',
                'before'
            );

            /**
             * ============================
             * Checkout E-learning
             * ============================
             */
            $el_rel  = 'dist/js/woo/checkout-elearning.js';
            $el_path = TCP_THEME_PATH . $el_rel;
            $el_url  = TCP_THEME_URI . $el_rel;

            wp_enqueue_script(
                'tcp-checkout-elearning',
                $el_url,
                ['wp-data', 'tcp-theme'],
                file_exists($el_path) ? (string) filemtime($el_path) : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
                true
            );

            wp_add_inline_script(
                'tcp-checkout-elearning',
                'window.TCP_ELEARNING = ' . wp_json_encode([
                    'ns' => 'tcp_elearning',
                    'messages' => [
                        'required' => 'Vui lòng nhập email tài khoản E-learning.',
                        'invalid'  => 'Email không hợp lệ.',
                    ],
                ]) . ';',
                'before'
            );
        }
    }
}
