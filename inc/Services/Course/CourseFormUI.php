<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseFormUI
{
    use Singleton;

    protected function init(): void
    {
        add_action('acf/init', [$this, 'register_acf_fields']);
    }

    public function register_acf_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        // Per-product overrides
        acf_add_local_field_group([
            'key'    => 'group_tcp_course_sidebar_form',
            'title'  => 'Form đăng ký (sidebar)',
            'fields' => [
                [
                    'key'  => 'field_tcp_course_form_title',
                    'name' => 'tcp_course_form_title',
                    'label' => 'Tiêu đề form (sidebar)',
                    'type' => 'text',
                    'instructions' => 'Để trống → dùng giá trị mặc định ở Theme Options.',
                ],
                [
                    'key'  => 'field_tcp_course_form_cf7_id',
                    'name' => 'tcp_course_form_cf7_id',
                    'label' => 'CF7 Form ID (override)',
                    'type' => 'text',
                    'instructions' => 'ID form Contact Form 7. Để trống → dùng giá trị mặc định ở Theme Options.',
                ],
                [
                    'key'  => 'field_tcp_course_form_consent_text',
                    'name' => 'tcp_course_form_consent_text',
                    'label' => 'Ghi chú quyền riêng tư (override)',
                    'type' => 'wysiwyg',
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
            ],
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'product']]],
            'menu_order' => 28,
            'position'   => 'normal',
        ]);

        // Global defaults (options page if exists, falls back to wp_options)
        acf_add_local_field_group([
            'key'    => 'group_tcp_course_sidebar_form_defaults',
            'title'  => 'Form đăng ký khóa học - Mặc định',
            'fields' => [
                [
                    'key'  => 'field_tcp_course_form_title_default',
                    'name' => 'tcp_course_form_title_default',
                    'label' => 'Tiêu đề form (mặc định)',
                    'type' => 'text',
                    'default_value' => 'Để lại thông tin để được tư vấn',
                ],
                [
                    'key'  => 'field_tcp_course_form_cf7_id_default',
                    'name' => 'tcp_course_form_cf7_id_default',
                    'label' => 'CF7 Form ID (mặc định)',
                    'type' => 'text',
                ],
                [
                    'key'  => 'field_tcp_course_form_consent_text_default',
                    'name' => 'tcp_course_form_consent_text_default',
                    'label' => 'Ghi chú quyền riêng tư (mặc định)',
                    'type' => 'wysiwyg',
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
            ],
            'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-general-settings']]],
        ]);
    }

    public function render(\WC_Product $product, array $data): void
    {
        $title = trim((string) ($data['tcp_course_form_title'] ?? ''));
        if ($title === '' && function_exists('get_field')) {
            $title = trim((string) get_field('tcp_course_form_title_default', 'option'));
        }

        $cf7_id = trim((string) ($data['tcp_course_form_cf7_id'] ?? ''));
        if ($cf7_id === '' && function_exists('get_field')) {
            $cf7_id = trim((string) get_field('tcp_course_form_cf7_id_default', 'option'));
        }

        $consent = trim((string) ($data['tcp_course_form_consent_text'] ?? ''));
        if ($consent === '' && function_exists('get_field')) {
            $consent = trim((string) get_field('tcp_course_form_consent_text_default', 'option'));
        }

        if ($cf7_id === '' || !shortcode_exists('contact-form-7')) {
            return;
        }

        $form_shortcode = '[contact-form-7 id="' . esc_attr($cf7_id) . '"]';
        $form_html = do_shortcode($form_shortcode);
        if (trim((string) $form_html) === '' || strpos($form_html, '[contact-form-7') === 0) {
            return;
        }

        ob_start();
        ?>
        <aside class="c-course-form" id="course-form" data-course-id="<?php echo esc_attr((string) $product->get_id()); ?>" data-course-name="<?php echo esc_attr(get_the_title($product->get_id())); ?>">
            <div class="c-course-form__card">
                <?php if ($title !== ''): ?>
                    <h3 class="c-course-form__title"><?php echo esc_html($title); ?></h3>
                <?php endif; ?>

                <div class="c-course-form__body">
                    <?php echo $form_html; ?>
                </div>

                <?php if ($consent !== ''): ?>
                    <div class="c-course-form__consent"><?php echo wp_kses_post($consent); ?></div>
                <?php endif; ?>
            </div>
        </aside>
        <?php
        echo trim(ob_get_clean());
    }
}
