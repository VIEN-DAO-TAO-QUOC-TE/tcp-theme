<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseAboutUI
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
            'key'    => 'group_tcp_course_about',
            'title'  => 'Về khóa học (5 trụ cột)',
            'fields' => [
                [
                    'key'  => 'field_tcp_course_about_eyebrow',
                    'name' => 'tcp_course_about_eyebrow',
                    'label' => 'Eyebrow',
                    'type' => 'text',
                    'placeholder' => 'VỀ KHÓA HỌC',
                ],
                [
                    'key'  => 'field_tcp_course_about_title',
                    'name' => 'tcp_course_about_title',
                    'label' => 'Tiêu đề',
                    'type' => 'text',
                ],
                [
                    'key'  => 'field_tcp_course_about_items',
                    'name' => 'tcp_course_about_items',
                    'label' => 'Danh sách trụ cột (icon + tiêu đề + mô tả)',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Thêm trụ cột',
                    'sub_fields' => [
                        [
                            'key' => 'field_tcp_course_about_item_icon',
                            'name' => 'icon',
                            'label' => 'Icon class',
                            'type' => 'text',
                            'default_value' => 'icon-circle-check',
                            'instructions' => 'Lucide icon class. Vd: icon-circle-check, icon-target, icon-users.',
                        ],
                        [
                            'key' => 'field_tcp_course_about_item_title',
                            'name' => 'title',
                            'label' => 'Tiêu đề trụ cột',
                            'type' => 'text',
                        ],
                        [
                            'key' => 'field_tcp_course_about_item_desc',
                            'name' => 'description',
                            'label' => 'Mô tả',
                            'type' => 'textarea',
                            'rows' => 2,
                        ],
                    ],
                ],
            ],
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'product']]],
            'menu_order' => 25,
            'position'   => 'normal',
        ]);
    }

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_about_eyebrow'] ?? ''));
        $title   = trim((string) ($data['tcp_course_about_title'] ?? ''));
        $items   = is_array($data['tcp_course_about_items'] ?? null) ? $data['tcp_course_about_items'] : [];

        if (empty($items) && $title === '') {
            return;
        }

        ob_start();
        ?>
        <section class="c-course-section c-course-about" id="about-course">
            <div class="c-course-about__inner">
                <?php if ($eyebrow !== '' || $title !== ''): ?>
                    <header class="c-course-section__header c-course-about__header">
                        <?php if ($eyebrow !== ''): ?>
                            <div class="c-course-section__eyebrow c-course-about__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                        <?php endif; ?>
                        <?php if ($title !== ''): ?>
                            <h2 class="c-course-section__title c-course-about__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>

                <?php if (!empty($items)): ?>
                    <ul class="c-course-about__list">
                        <?php foreach ($items as $row):
                            $icon = trim((string) ($row['icon'] ?? 'icon-circle-check'));
                            $row_title = trim((string) ($row['title'] ?? ''));
                            $row_desc  = trim((string) ($row['description'] ?? ''));
                            if ($row_title === '' && $row_desc === '') continue;
                        ?>
                            <li class="c-course-about__item">
                                <span class="c-course-about__icon <?php echo esc_attr($icon); ?>" aria-hidden="true"></span>
                                <div class="c-course-about__body">
                                    <?php if ($row_title !== ''): ?>
                                        <h3 class="c-course-about__itemTitle"><?php echo esc_html($row_title); ?></h3>
                                    <?php endif; ?>
                                    <?php if ($row_desc !== ''): ?>
                                        <p class="c-course-about__itemDesc"><?php echo esc_html($row_desc); ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
        <?php
        echo trim(ob_get_clean());
    }
}
