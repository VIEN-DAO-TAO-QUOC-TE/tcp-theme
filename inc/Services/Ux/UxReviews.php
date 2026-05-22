<?php

namespace TCP\Theme\Services\Ux;

use TCP\Theme\Core\Singleton;
use WP_Query;

defined('ABSPATH') || exit;

final class UxReviews
{
    use Singleton;

    protected function init(): void
    {
        add_action('ux_builder_setup', [$this, 'registerUx'], 999);

        // shortcode WordPress (để test / để UX builder gọi)
        add_shortcode('tcp_reviews', [$this, 'renderShortcode']);
    }

    public function registerUx(): void
    {
        if (!function_exists('add_ux_builder_shortcode')) return;

        add_ux_builder_shortcode('tcp_reviews', [
            'name'     => 'TCP Reviews',
            'category' => 'TCP',
            'info'     => '{{ perpage }} reviews',
            'wrap'     => false,

            'options' => [
                'perpage' => [
                    'type' => 'textfield',
                    'heading' => 'Số lượng',
                    'default' => 6,
                ],

                'orderby' => [
                    'type' => 'select',
                    'heading' => 'Orderby',
                    'options' => [
                        'date' => 'Date',
                        'ID' => 'ID',
                        'title' => 'Title',
                        'rand' => 'Random',
                        'menu_order' => 'Menu order',
                    ],
                    'default' => 'date',
                ],

                'order' => [
                    'type' => 'select',
                    'heading' => 'Order',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC',
                    ],
                    'default' => 'DESC',
                ],

                'review_ids' => [
                    'type' => 'select',
                    'heading' => 'Chọn review (tuỳ chọn)',
                    'param_name' => 'review_ids',
                    'config' => [
                        'multiple' => true,
                        'placeholder' => 'Select..',
                        'postSelect' => [
                            'post_type' => 'tcp_review',
                        ],
                    ],
                ],

                'class' => [
                    'type' => 'textfield',
                    'heading' => 'CSS class bổ sung (row)',
                    'default' => '',
                    'full_width' => true,
                ],
            ],
        ]);
    }

    public function renderShortcode($atts, $content = null): string
    {
        $atts = shortcode_atts([
            'perpage'   => 6,
            'orderby'   => 'date',
            'order'     => 'DESC',
            'review_ids' => '',
            'class'     => '',
        ], (array) $atts, 'tcp_reviews');

        $perpage = (int) $atts['perpage'];
        if ($perpage === 0) $perpage = 6;

        $ids = $this->normalizeIds($atts['review_ids']);

        $args = [
            'post_type'           => 'tcp_review',
            'post_status'         => 'publish',
            'posts_per_page'      => $perpage,
            'orderby'             => $atts['orderby'] ?: 'date',
            'order'               => strtoupper($atts['order']) === 'ASC' ? 'ASC' : 'DESC',
            'ignore_sticky_posts' => true,
        ];

        if (!empty($ids)) {
            $args['post__in'] = $ids;
            $args['orderby']  = 'post__in';
        }

        $q = new WP_Query($args);

        // row wrapper theo đúng design
        $rowClass = trim('align-equal home-testimonials__slider c-slider js-slider-nav ' . (string) $atts['class']);

        $out  = '[row class="' . esc_attr($rowClass) . '"]';

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $pid = get_the_ID();

                // ACF fields (theo json bạn export)
                $content = (string) get_field('review_content', $pid);
                $name    = (string) get_field('review_student_name', $pid);
                $title   = (string) get_field('review_student_title', $pid);
                $company = (string) get_field('review_student_company', $pid);

                $courseObj  = get_field('review_course', $pid); // post_object
                $courseName = (string) get_field('review_course_name', $pid);

                if (empty($courseName) && $courseObj) {
                    // ACF post_object có thể là WP_Post hoặc ID
                    $courseId = is_object($courseObj) ? (int) $courseObj->ID : (int) $courseObj;
                    if ($courseId > 0) $courseName = get_the_title($courseId);
                }

                $avatar = get_field('review_avatar', $pid); // image (ID/array tùy setting)
                $avatarUrl = $this->resolveImageUrl($avatar, 'medium');

                // Fallback name (nếu chưa nhập)
                if ($name === '') $name = get_the_title($pid);

                // Quote icon
                $quoteIcon = defined('TCP_THEME_URI')
                    ? TCP_THEME_URI . 'assets/images/icons/quote.svg'
                    : '';

                // sanitize content: giữ thẻ cơ bản, tránh vỡ layout
                $quoteHtml = $this->sanitizeQuoteHtml($content);

                // meta line giống mẫu: ưu tiên course, nếu không có thì dùng title/company
                $meta = '';
                if ($courseName !== '') {
                    $meta = 'Học viên lớp “' . esc_html($courseName) . '”';
                } else {
                    $parts = array_values(array_filter([trim($title), trim($company)]));
                    if (!empty($parts)) $meta = esc_html(implode(' • ', $parts));
                }

                $alt = 'Ảnh đại diện ' . $name;

                $html  = '<figure class="c-testimonial-card">';
                $html .= '  <figcaption class="c-testimonial-card__footer">';
                $html .= '    <span class="c-testimonial-card__avatar">';

                if ($avatarUrl) {
                    $html .= '      <img src="' . esc_url($avatarUrl) . '" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async" />';
                } else {
                    // fallback nếu chưa có avatar
                    $html .= '      <img src="https://placehold.co/400x400/png" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async" />';
                }

                $html .= '    </span>';
                $html .= '    <div class="c-testimonial-card__author">';
                $html .= '      <p class="c-testimonial-card__name">' . esc_html($name) . '</p>';
                if ($meta !== '') {
                    $html .= '      <p class="c-testimonial-card__meta">' . $meta . '</p>';
                }
                $html .= '    </div>';
                $html .= '  </figcaption>';
                $html .= '  <div class="divider-line"></div>';
                $html .= '  <div class="c-testimonial-card__quote" aria-hidden="true">';

                if ($quoteIcon) {
                    $html .= '    <img src="' . esc_url($quoteIcon) . '" alt="" loading="lazy" decoding="async" />';
                }

                $html .= '    <blockquote class="c-testimonial-card__content">' . $quoteHtml . '</blockquote>';
                $html .= '  </div>';
                $html .= '</figure>';

                // bọc đúng cấu trúc Flatsome builder shortcodes
                $out .= '[col span="4" span__sm="12"]';
                $out .= '[ux_html]' . $html . '[/ux_html]';
                $out .= '[/col]';
            }
        }

        $out .= '[/row]';

        wp_reset_postdata();

        // quan trọng: để WP parse [row][col][ux_html]
        return do_shortcode($out);
    }

    private function normalizeIds($value): array
    {
        if (is_array($value)) {
            $ids = $value;
        } else {
            $value = (string) $value;
            if ($value === '') return [];
            $ids = explode(',', $value);
        }

        $ids = array_map('absint', $ids);
        return array_values(array_filter($ids));
    }

    private function resolveImageUrl($acfImage, string $size = 'medium'): string
    {
        // ACF image có thể trả: ID hoặc array
        if (is_numeric($acfImage)) {
            $url = wp_get_attachment_image_url((int) $acfImage, $size);
            return $url ? (string) $url : '';
        }

        if (is_array($acfImage)) {
            // nếu có sizes
            if (!empty($acfImage['sizes'][$size])) return (string) $acfImage['sizes'][$size];
            if (!empty($acfImage['url'])) return (string) $acfImage['url'];
        }

        return '';
    }

    private function sanitizeQuoteHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') return '';

        // bỏ <p> bao ngoài nếu có, để tránh xuống dòng kỳ
        // nhưng vẫn cho phép <br>, <em>, <strong>...
        $allowed = [
            'br' => [],
            'em' => [],
            'strong' => [],
            'b' => [],
            'i' => [],
            'u' => [],
            'span' => ['class' => true],
        ];

        $html = wp_kses($html, $allowed);

        // nếu còn entity/space dư
        return trim($html);
    }
}
