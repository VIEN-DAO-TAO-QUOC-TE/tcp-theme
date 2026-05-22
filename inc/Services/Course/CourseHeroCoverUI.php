<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseHeroCoverUI
{
    use Singleton;

    /**
     * Render cover image section for course product
     */
    public function render(\WC_Product $product, array $data): void
    {
        $cover_id = (int) ($data['tcp_course_hero_cover_image'] ?? 0);

        // fallback: Woo product thumbnail
        $fallback_id = (int) $product->get_image_id();

        $img_html = '';

        if ($cover_id > 0) {
            $img_html = wp_get_attachment_image(
                $cover_id,
                'full',
                false,
                [
                    'class' => 'c-course-hero-cover__img',
                    'loading' => 'eager',
                    'decoding' => 'async',
                ]
            );
        } elseif ($fallback_id > 0) {
            $img_html = wp_get_attachment_image(
                $fallback_id,
                'full',
                false,
                [
                    'class' => 'c-course-hero-cover__img',
                    'loading' => 'eager',
                    'decoding' => 'async',
                ]
            );
        }

        // Nếu không có ảnh nào thì khỏi render
        if ($img_html === '') return;

        echo '<section class="c-course-section c-course-hero-cover" id="hero-cover">';
        echo '  <div class="c-course-hero-cover__inner">';
        echo '    <div class="c-course-hero-cover__media">';
        echo        $img_html;
        echo '    </div>';
        echo '  </div>';
        echo '</section>';
    }
}
