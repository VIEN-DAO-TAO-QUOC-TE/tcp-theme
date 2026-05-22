<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class WooToast
{
    use Singleton;

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 20);
        add_action('wp_head', [$this, 'add_js_flag'], 1);
    }

    public function add_js_flag(): void
    {
        // để CSS hide notice ngay từ đầu, tránh flash
        echo "<script>document.documentElement.classList.add('tcp-js');</script>\n";
    }

    public function enqueue(): void
    {
        if (!function_exists('is_woocommerce')) return;

        // if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) return;

        // $ver = wp_get_theme()->get('Version');

        // JS toast
         wp_enqueue_script(
            'tcp-wc-toasts',
            TCP_THEME_URI . 'dist/js/wc-toasts.js',
            [],
            // ['jquery', 'toastify'],
            file_exists(TCP_THEME_PATH . 'dist/js/wc-toasts.js') ? (string) filemtime(TCP_THEME_PATH . 'dist/js/wc-toasts.js') : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
            true
        );

        // CSS toast + hide notice gốc
        wp_enqueue_style(
            'tcp-wc-toasts',
            TCP_THEME_URI .  'dist/css/wc-toasts.css',
            [],
           file_exists(TCP_THEME_PATH . 'dist/css/wc-toasts.css') ? (string) filemtime(TCP_THEME_PATH . 'dist/css/wc-toasts.css') : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
        );

        // Toastify
        // wp_enqueue_style(
        //     'toastify',
        //     'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css',
        //     [],
        //     null
        // );
        // wp_enqueue_script(
        //     'toastify',
        //     'https://cdn.jsdelivr.net/npm/toastify-js',
        //     [],
        //     null,
        //     true
        // );

        // Script custom bắt notices Woo -> Toastify
        // wp_enqueue_script(
        //     'tcp-wc-toasts',
        //     TCP_THEME_URI . 'dist/js/wc-toasts.js',
        //     ['jquery', 'toastify'],
        //     file_exists(TCP_THEME_PATH . 'dist/js/wc-toasts.js') ? (string) filemtime(TCP_THEME_PATH . 'dist/js/wc-toasts.js') : (defined('TCP_THEME_VERSION') ? TCP_THEME_VERSION : null),
        //     true
        // );
    }
}
