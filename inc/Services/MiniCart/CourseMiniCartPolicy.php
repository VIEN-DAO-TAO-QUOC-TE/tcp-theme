<?php

namespace TCP\Theme\Services\MiniCart;

use TCP\Theme\Core\Singleton;
use WC_Product;

defined('ABSPATH') || exit;

final class CourseMiniCartPolicy
{
    use Singleton;

    /**
     * Nếu muốn ép khóa học qty=1 theo category,
     * đổi slug này theo site (ví dụ: "khoa-hoc", "courses"...)
     */
    private const COURSE_CAT_SLUG = 'course';

    protected function init(): void
    {
        // 1) Không hiển thị UI +/- quantity trong mini cart của Flatsome
        add_filter('flatsome_show_mini_cart_item_quantity', [$this, 'disable_flatsome_mini_cart_qty'], 20, 2);

        // 2) Thay chỗ "1 × price" -> chỉ hiện giá (có sale)
        add_filter('woocommerce_widget_cart_item_quantity', [$this, 'widget_cart_item_price_only'], 99, 3);

        // 3) (Optional) Ép khóa học là sold individually (qty luôn = 1 mọi nơi)
        add_filter('woocommerce_is_sold_individually', [$this, 'force_course_sold_individually'], 20, 2);

        remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10);
        remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20);

        add_action('woocommerce_widget_shopping_cart_buttons', [$this, 'woocommerce_widget_shopping_cart_proceed_to_checkout'], 20);
        remove_action('woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal', 10);
        add_action('woocommerce_widget_shopping_cart_total',  [$this, 'woocommerce_widget_shopping_cart_subtotal'], 10);
    }

    public function disable_flatsome_mini_cart_qty($show, $cart_item_key): bool
    {
        // Tắt hoàn toàn UI qty ở mini cart (phù hợp bán khóa học)
        return false;
    }

    public function widget_cart_item_price_only_bk($html, $cart_item, $cart_item_key): string
    {
        if (empty($cart_item['data']) || ! $cart_item['data'] instanceof WC_Product) {
            return $html;
        }

        $product = $cart_item['data'];

        // Luôn build đúng regular + sale
        $price_html = (new class {
            public function render(WC_Product $product): string
            {
                $regular = (float) $product->get_regular_price();
                $sale    = (float) $product->get_sale_price();
                $current = (float) $product->get_price();

                if ($regular <= 0 || $sale <= 0 || $sale >= $regular || $current >= $regular) {
                    return '<span class="amount">' . wc_price($current) . '</span>';
                }

                return sprintf(
                    '<del class="tcp-price tcp-price--regular">%s</del> <ins class="tcp-price tcp-price--sale">%s</ins>',
                    wc_price($regular),
                    wc_price($current)
                );
            }
        })->render($product);

        return '<span class="tcp-mini-cart__price price">' . $price_html . '</span>';
    }
    public function widget_cart_item_price_only($html, $cart_item, $cart_item_key): string
    {
        if (empty($cart_item['data']) || ! $cart_item['data'] instanceof WC_Product) {
            return $html;
        }

        $product = $cart_item['data'];
        $qty     = isset($cart_item['quantity']) ? (int) $cart_item['quantity'] : 0;
        if ($qty < 1) $qty = 1;

        // Build đúng regular + sale (đơn giá)
        $price_html = (new class {
            public function render(WC_Product $product): string
            {
                $regular = (float) $product->get_regular_price();
                $sale    = (float) $product->get_sale_price();
                $current = (float) $product->get_price();

                if ($regular <= 0 || $sale <= 0 || $sale >= $regular || $current >= $regular) {
                    return '<span class="amount">' . wc_price($current) . '</span>';
                }

                return sprintf(
                    '<del class="tcp-price tcp-price--regular">%s</del> <ins class="tcp-price tcp-price--sale">%s</ins>',
                    wc_price($regular),
                    wc_price($current)
                );
            }
        })->render($product);

        // Hiển thị số lượng + giá
        // Dạng: "x2 × 700.000đ" (vẫn là đơn giá, tổng line bạn đang hiển thị ở chỗ khác nếu cần)
        $qty_html = '';

        if ($qty > 1) {
            $qty_html =  esc_html((string) $qty) . ' x ';
        }

        return '<span class="tcp-mini-cart__price price">' . $qty_html . $price_html . '</span>';
    }


    public function force_course_sold_individually($sold_individually, $product): bool
    {
        if (! $product instanceof WC_Product) {
            return (bool) $sold_individually;
        }

        // Nếu không muốn ép theo category thì return $sold_individually;
        // if ($this->is_course_product($product->get_id())) {
        //     return true;
        // }

        return (bool) $sold_individually;
    }

    private function is_course_product(int $product_id): bool
    {
        // Điều kiện theo category slug
        return has_term(self::COURSE_CAT_SLUG, 'product_cat', $product_id);
    }
    public function woocommerce_widget_shopping_cart_proceed_to_checkout()
    {
        $wp_button_class = wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '';
        echo '<a href="' . esc_url(wc_get_checkout_url()) . '" class="button primary checkout wc-forward' . esc_attr($wp_button_class) . '">' . esc_html__('Checkout', 'woocommerce') . '</a>';
    }
    public function woocommerce_widget_shopping_cart_subtotal()
    {
        echo '<strong>' . esc_html__('Tạm tính:', 'woocommerce') . '</strong> ' . WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
