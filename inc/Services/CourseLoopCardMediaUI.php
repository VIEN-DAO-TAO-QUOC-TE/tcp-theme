<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseLoopCardMediaUI
{
    use Singleton;

    protected function init(): void
    {
        // Remove thumbnails mặc định & add trainer overlay (chạy muộn để chắc chắn remove được)
        add_action('after_setup_theme', [$this, 'register'], 20);
    }

    public function register(): void
    {
        $this->remove_default_loop_thumbs();

        // Chỉ render trainer thumb trong vùng image
        add_action('flatsome_woocommerce_shop_loop_images', [$this, 'output_trainer_thumb'], 5);
    }

    private function remove_default_loop_thumbs(): void
    {
        remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('flatsome_woocommerce_shop_loop_images', 'woocommerce_template_loop_product_thumbnail', 10);
        remove_action('flatsome_woocommerce_shop_loop_images', 'woocommerce_get_alt_product_thumbnail', 11);
        remove_action('flatsome_woocommerce_shop_loop_images', 'flatsome_woocommerce_get_alt_product_thumbnail', 11);

        remove_action('flatsome_woocommerce_shop_loop_images', 'woocommerce_get_alt_product_thumbnail', 12);
        remove_action('flatsome_woocommerce_shop_loop_images', 'flatsome_woocommerce_get_alt_product_thumbnail', 12);
    }

    /**
     * Lấy bg url từ ACF
     */
    public function get_card_bg_url(int $product_id, bool $fallback_featured = false): string
    {
        $product_id = absint($product_id);
        if (!$product_id) return '';

        $bg_id = function_exists('get_field') ? (int) get_field('tcp_course_card_bg_image', $product_id) : 0;
        $bg_url = $bg_id ? (string) wp_get_attachment_image_url($bg_id, 'large') : '';

        if (!$bg_url && $fallback_featured) {
            $thumb_id = (int) get_post_thumbnail_id($product_id);
            if ($thumb_id) $bg_url = (string) wp_get_attachment_image_url($thumb_id, 'large');
        }

        return $bg_url ?: '';
    }

    /**
     * Trả về attribute để gắn vào <div class="col-inner"...>
     * Dùng CSS var để SCSS xử lý: background-image: var(--tcp-course-card-bg);
     */
    public function col_inner_bg_attr(int $product_id): string
    {
        $url = $this->get_card_bg_url($product_id, false);
        if (!$url) return '';

        // CSS var với url(...)
        $css = sprintf("--tcp-course-card-bg: url('%s');", esc_url($url));

        return ' style="' . esc_attr($css) . '"';
    }

    /**
     * Render trainer thumb (transparent PNG) trong box-image.
     */
    public function output_trainer_thumb(): void
    {
        $product_id = get_the_ID();
        if (!$product_id) return;

        $trainer_id = function_exists('get_field') ? (int) get_field('tcp_course_card_trainer_thumb', $product_id) : 0;
        if (!$trainer_id) return;

        $trainer_url = wp_get_attachment_image_url($trainer_id, 'medium');
        if (!$trainer_url) return;

        echo '<div class="c-courseCardMedia">';
        echo '  <span class="c-courseCardMedia__spacer" aria-hidden="true"></span>';
        echo '  <img width="247" height="139" class="c-courseCardMedia__trainer woocommerce-placeholder wp-post-image" src="' . esc_url($trainer_url) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy" decoding="async" />';
        echo '</div>';
    }
}
