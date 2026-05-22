<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class Woo
{
    use Singleton;

    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'support']);
        add_filter('woocommerce_enqueue_styles', '__return_empty_array');

        // Buttons (Woo core)
        add_filter('woocommerce_button_class', [$this, 'button_class'], 10, 2);

        // Loop add-to-cart button (<a>)
        add_filter('woocommerce_loop_add_to_cart_link', [$this, 'loop_add_to_cart_class'], 10, 3);

        // Form fields (checkout/account/coupon...)
        add_filter('woocommerce_form_field_args', [$this, 'form_field_args'], 10, 3);

        // Quantity input classes
        add_filter('woocommerce_quantity_input_classes', [$this, 'quantity_input_classes'], 10, 2);

        // Notices wrapper classes (still override templates below for best result)
        add_filter('woocommerce_add_notice', [$this, 'capture_notice_type'], 10, 2);

        // Optional: body class for easier page-level styling
        add_filter('body_class', [$this, 'body_class']);
    }

    public function support(): void
    {
        add_theme_support('woocommerce');
    }

    /**
     * Add theme class to default Woo buttons (button, a.button, input.button)
     */
    public function button_class(string $class, $button = null): string
    {
        // Avoid duplicating if already has btn*
        if (strpos($class, 'btn') !== false) {
            return $class;
        }
        return trim($class . ' btn btn-primary');
    }

    /**
     * Add shadcn button class to loop add-to-cart link
     */
    public function loop_add_to_cart_class(string $html, $product, $args): string
    {
        // Add our btn classes into <a class="...">
        if (strpos($html, 'class="') !== false) {
            $html = preg_replace('/class="([^"]*)"/', 'class="$1 btn btn-primary"', $html, 1);
        }
        return $html;
    }

    /**
     * Style WooCommerce form fields with Tailwind primitives
     */
    public function form_field_args(array $args, string $key, $value): array
    {
        // Field wrapper class
        $args['class'] = array_filter(array_merge($args['class'] ?? [], [
            'tcp-wc-field',
            'mb-4',
        ]));

        // Label class
        $args['label_class'] = array_filter(array_merge($args['label_class'] ?? [], [
            'label',
            'block',
            'mb-2',
        ]));

        // Input class
        $inputClass = 'input';

        if (!empty($args['type'])) {
            if (in_array($args['type'], ['select'], true)) {
                $inputClass = 'select';
            }
            if (in_array($args['type'], ['textarea'], true)) {
                $inputClass = 'textarea';
            }
            if (in_array($args['type'], ['checkbox', 'radio'], true)) {
                // checkbox/radio: use smaller styling, leave native mostly
                $inputClass = '';
            }
        }

        // Merge into input_class (Woo uses this for <input>, <select>, <textarea>)
        $args['input_class'] = array_filter(array_merge($args['input_class'] ?? [], $inputClass ? [$inputClass] : []));

        // Description/help text
        $args['description_class'] = array_filter(array_merge($args['description_class'] ?? [], [
            'help-text',
            'mt-1',
        ]));

        // Required marker style (optional)
        $args['required'] = $args['required'] ?? false;

        return $args;
    }

    /**
     * Quantity input styling
     */
    public function quantity_input_classes(array $classes, $product): array
    {
        $classes[] = 'input';
        $classes[] = 'max-w-[96px]';
        $classes[] = 'text-center';
        return array_values(array_unique($classes));
    }

    /**
     * Track notice type (optional hook point, mainly for debugging / extensibility)
     */
    public function capture_notice_type($message, $notice_type = 'success')
    {
        // noop: leave this for future usage (analytics/debug)
        return $message;
    }

    public function body_class(array $classes): array
    {
        if (function_exists('is_woocommerce') && is_woocommerce()) {
            $classes[] = 'tcp-woocommerce';
        }
        if (function_exists('is_cart') && is_cart()) {
            $classes[] = 'tcp-cart';
        }
        if (function_exists('is_checkout') && is_checkout()) {
            $classes[] = 'tcp-checkout';
        }
        if (function_exists('is_account_page') && is_account_page()) {
            $classes[] = 'tcp-account';
        }
        return $classes;
    }
}
