<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseMethodologyUI
{
    use Singleton;

    protected function init(): void
    {
        add_action('acf/init', [$this, 'register_acf_fields']);
    }

    public function register_acf_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key'    => 'group_tcp_course_methodology',
            'title'  => 'Phương pháp (UPWARDS™…)',
            'fields' => [
                [
                    'key'  => 'field_tcp_course_methodology_cover',
                    'name' => 'tcp_course_methodology_cover',
                    'label' => 'Ảnh cover phương pháp',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                ],
                [
                    'key'  => 'field_tcp_course_methodology_eyebrow',
                    'name' => 'tcp_course_methodology_eyebrow',
                    'label' => 'Eyebrow',
                    'type' => 'text',
                    'placeholder' => 'PHƯƠNG PHÁP',
                ],
                [
                    'key'  => 'field_tcp_course_methodology_title',
                    'name' => 'tcp_course_methodology_title',
                    'label' => 'Tiêu đề',
                    'type' => 'text',
                ],
                [
                    'key'  => 'field_tcp_course_methodology_intro',
                    'name' => 'tcp_course_methodology_intro',
                    'label' => 'Đoạn mô tả ngắn',
                    'type' => 'textarea',
                    'rows' => 2,
                ],
                [
                    'key'  => 'field_tcp_course_methodology_items',
                    'name' => 'tcp_course_methodology_items',
                    'label' => 'Bullet items',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'button_label' => 'Thêm bullet',
                    'sub_fields' => [
                        [
                            'key' => 'field_tcp_course_methodology_item_icon',
                            'name' => 'icon',
                            'label' => 'Icon class',
                            'type' => 'text',
                            'default_value' => 'icon-circle-check',
                        ],
                        [
                            'key' => 'field_tcp_course_methodology_item_text',
                            'name' => 'text',
                            'label' => 'Nội dung',
                            'type' => 'textarea',
                            'rows' => 2,
                        ],
                    ],
                ],
            ],
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'product']]],
            'menu_order' => 26,
            'position'   => 'normal',
        ]);
    }

    public function render(\WC_Product $product, array $data): void
    {
        $cover_id = (int) ($data['tcp_course_methodology_cover'] ?? 0);
        $eyebrow  = trim((string) ($data['tcp_course_methodology_eyebrow'] ?? ''));
        $title    = trim((string) ($data['tcp_course_methodology_title'] ?? ''));
        $intro    = trim((string) ($data['tcp_course_methodology_intro'] ?? ''));
        $items    = is_array($data['tcp_course_methodology_items'] ?? null) ? $data['tcp_course_methodology_items'] : [];

        if ($title === '' && $cover_id <= 0 && empty($items)) {
            return;
        }

        $cover_html = '';
        if ($cover_id > 0) {
            $cover_html = wp_get_attachment_image($cover_id, 'large', false, [
                'class' => 'c-course-methodology__coverImg',
                'loading' => 'lazy',
                'decoding' => 'async',
            ]);
        }

        ob_start();
        ?>
        <section class="c-course-section c-course-methodology" id="methodology">
            <div class="c-course-methodology__inner">
                <?php if ($cover_html !== ''): ?>
                    <div class="c-course-methodology__cover"><?php echo $cover_html; ?></div>
                <?php endif; ?>

                <div class="c-course-methodology__body">
                    <?php if ($eyebrow !== '' || $title !== '' || $intro !== ''): ?>
                        <header class="c-course-section__header c-course-methodology__header">
                            <?php if ($eyebrow !== ''): ?>
                                <div class="c-course-section__eyebrow c-course-methodology__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                            <?php endif; ?>
                            <?php if ($title !== ''): ?>
                                <h2 class="c-course-section__title c-course-methodology__title"><?php echo esc_html($title); ?></h2>
                            <?php endif; ?>
                            <?php if ($intro !== ''): ?>
                                <p class="c-course-section__desc c-course-methodology__intro"><?php echo esc_html($intro); ?></p>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <?php if (!empty($items)): ?>
                        <ul class="c-course-methodology__list">
                            <?php foreach ($items as $row):
                                $icon = trim((string) ($row['icon'] ?? 'icon-circle-check'));
                                $text = trim((string) ($row['text'] ?? ''));
                                if ($text === '') continue;
                            ?>
                                <li class="c-course-methodology__item">
                                    <span class="c-course-methodology__icon <?php echo esc_attr($icon); ?>" aria-hidden="true"></span>
                                    <span class="c-course-methodology__text"><?php echo esc_html($text); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        echo trim(ob_get_clean());
    }
}
