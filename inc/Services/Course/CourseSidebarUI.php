<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseSidebarUI
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
            'key'    => 'group_tcp_course_sidebar_summary',
            'title'  => 'Sidebar khóa học (tóm tắt)',
            'fields' => [
                [
                    'key'          => 'field_tcp_course_sidebar_tagline',
                    'name'         => 'tcp_course_sidebar_tagline',
                    'label'        => 'Tagline (dòng dưới tiêu đề, màu cam)',
                    'type'         => 'text',
                    'instructions' => 'Vd: "Trở thành quản lý thực thụ trong 12 buổi"',
                ],
                [
                    'key'       => 'field_tcp_course_sidebar_description',
                    'name'      => 'tcp_course_sidebar_description',
                    'label'     => 'Mô tả ngắn (sidebar)',
                    'type'      => 'textarea',
                    'rows'      => 5,
                    'new_lines' => 'wpautop',
                ],
                [
                    'key'          => 'field_tcp_course_sidebar_location',
                    'name'         => 'tcp_course_sidebar_location',
                    'label'        => 'Địa điểm học',
                    'type'         => 'text',
                    'instructions' => 'Vd: "5 Trương Quốc Dung, P. Phú Nhuận, TP.HCM". Để trống → ẩn meta item.',
                ],
            ],
            'location'   => [[['param' => 'post_type', 'operator' => '==', 'value' => 'product']]],
            'menu_order' => 18,
            'position'   => 'normal',
        ]);
    }

    public function render(\WC_Product $product, array $data): void
    {
        $product_id = $product->get_id();

        $term  = $this->get_primary_term($product_id, 'product_cat');
        $title = get_the_title($product_id);

        $tagline     = trim((string) ($data['tcp_course_sidebar_tagline'] ?? ''));
        $description = trim((string) ($data['tcp_course_sidebar_description'] ?? ''));
        $location    = trim((string) ($data['tcp_course_sidebar_location'] ?? ''));

        $rating      = (float) $product->get_average_rating();
        $rating_text = $rating > 0 ? $this->format_vi_decimal($rating, 1) : '';

        $students_source = (string) ($data['tcp_course_students_source'] ?? 'total_sales');
        if ($students_source === 'override') {
            $students = (int) ($data['tcp_course_students_override'] ?? 0);
        } else {
            $students = (int) $product->get_total_sales();
        }

        $start_date_text = $this->resolve_start_date($product_id, $data);

        $has_meta = $rating_text !== '' || $students > 0 || $start_date_text !== '' || $location !== '';

        ob_start();
?>
        <!-- TCP: sidebar start -->
        <aside class="c-course-sidebar" id="course-sidebar">
            <div class="c-course-sidebar__card">
                <?php if (!empty($term['name'])): ?>
                    <?php
                    $eyebrow_text = function_exists('mb_strtoupper') ? mb_strtoupper($term['name'], 'UTF-8') : strtoupper($term['name']);
                    ?>
                    <?php if (!empty($term['url'])): ?>
                        <a class="c-course-sidebar__eyebrow" href="<?php echo esc_url($term['url']); ?>"><?php echo esc_html($eyebrow_text); ?></a>
                    <?php else: ?>
                        <span class="c-course-sidebar__eyebrow"><?php echo esc_html($eyebrow_text); ?></span>
                    <?php endif; ?>
                <?php endif; ?>

                <h2 class="c-course-sidebar__title"><?php echo esc_html($title); ?></h2>

                <?php if ($tagline !== ''): ?>
                    <p class="c-course-sidebar__tagline"><?php echo esc_html($tagline); ?></p>
                <?php endif; ?>

                <?php if ($description !== ''): ?>
                    <div class="c-course-sidebar__desc"><?php echo wp_kses_post(wpautop($description)); ?></div>
                <?php endif; ?>

                <?php if ($has_meta): ?>
                    <div class="c-course-sidebar__meta">
                        <?php if ($rating_text !== ''): ?>
                            <span class="c-course-sidebar__metaItem">
                                <span class="c-course-sidebar__metaIcon icon-star" aria-hidden="true"></span>
                                <span class="c-course-sidebar__metaValue"><?php echo esc_html(sprintf(__('%s sao đánh giá từ học viên', 'tcp-theme'), $rating_text)); ?></span>
                            </span>
                        <?php endif; ?>

                        <?php if ($students > 0): ?>
                            <span class="c-course-sidebar__metaItem">
                                <span class="c-course-sidebar__metaIcon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M10.6673 14V12.6667C10.6673 11.9594 10.3864 11.2811 9.88627 10.781C9.38617 10.281 8.70789 10 8.00065 10H4.00065C3.29341 10 2.61513 10.281 2.11503 10.781C1.61494 11.2811 1.33398 11.9594 1.33398 12.6667V14" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.666 2.08533C11.2379 2.23357 11.7443 2.5675 12.1058 3.0347C12.4673 3.5019 12.6635 4.07592 12.6635 4.66666C12.6635 5.2574 12.4673 5.83142 12.1058 6.29862C11.7443 6.76582 11.2379 7.09975 10.666 7.24799" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14.666 14V12.6667C14.6656 12.0758 14.4689 11.5019 14.1069 11.0349C13.7449 10.5679 13.2381 10.2344 12.666 10.0867" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6.00065 7.33333C7.47341 7.33333 8.66732 6.13943 8.66732 4.66667C8.66732 3.19391 7.47341 2 6.00065 2C4.52789 2 3.33398 3.19391 3.33398 4.66667C3.33398 6.13943 4.52789 7.33333 6.00065 7.33333Z" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="c-course-sidebar__metaValue"><?php echo esc_html(sprintf(__('%s học viên', 'tcp-theme'), number_format_i18n($students))); ?></span>
                            </span>
                        <?php endif; ?>

                        <?php if ($start_date_text !== ''): ?>
                            <span class="c-course-sidebar__metaItem">
                                <span class="c-course-sidebar__metaIcon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M5.33398 1.33337V4.00004" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.666 1.33337V4.00004" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.6667 2.66663H3.33333C2.59695 2.66663 2 3.26358 2 3.99996V13.3333C2 14.0697 2.59695 14.6666 3.33333 14.6666H12.6667C13.403 14.6666 14 14.0697 14 13.3333V3.99996C14 3.26358 13.403 2.66663 12.6667 2.66663Z" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M2 6.66663H14" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="c-course-sidebar__metaValue"><?php echo esc_html(sprintf(__('Khai giảng: %s', 'tcp-theme'), $start_date_text)); ?></span>
                            </span>
                        <?php endif; ?>

                        <?php if ($location !== ''): ?>
                            <span class="c-course-sidebar__metaItem">
                                <span class="c-course-sidebar__metaIcon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M13.3327 6.66671C13.3327 9.99537 9.64002 13.462 8.40002 14.5327C8.2845 14.6196 8.14388 14.6665 7.99935 14.6665C7.85482 14.6665 7.7142 14.6196 7.59868 14.5327C6.35868 13.462 2.66602 9.99537 2.66602 6.66671C2.66602 5.25222 3.22792 3.89567 4.22811 2.89547C5.22831 1.89528 6.58486 1.33337 7.99935 1.33337C9.41384 1.33337 10.7704 1.89528 11.7706 2.89547C12.7708 3.89567 13.3327 5.25222 13.3327 6.66671Z" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 8.66663C9.10457 8.66663 10 7.7712 10 6.66663C10 5.56206 9.10457 4.66663 8 4.66663C6.89543 4.66663 6 5.56206 6 6.66663C6 7.7712 6.89543 8.66663 8 8.66663Z" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="c-course-sidebar__metaValue"><?php echo esc_html($location); ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
        <!-- TCP: sidebar end -->
<?php
        echo trim(ob_get_clean());
    }

    private function format_vi_decimal(float $num, int $decimals = 1): string
    {
        return str_replace('.', ',', number_format($num, $decimals, '.', ''));
    }

    private function resolve_start_date(int $product_id, array $data): string
    {
        $date_keys = [
            'tcp_course_start_date',
            'tcp_course_start_at',
            'tcp_course_start_time',
            'tcp_course_start',
            'tcp_course_start_text',
            'tcp_course_schedule_text',
            'tcp_course_opening_date',
            'tcp_course_opening_at',
            'tcp_course_card_start_date',
            'tcp_course_date_start',
            'course_start_date',
            'course_start_at',
            'course_start',
            'ngay_bat_dau',
            'start_date',
        ];

        foreach ($date_keys as $key) {
            if (!isset($data[$key])) continue;
            $formatted = $this->format_course_date_value($data[$key]);
            if ($formatted !== '') return $formatted;
        }

        $from_tree = $this->scan_start_date_in_tree($data);
        if ($from_tree !== '') return $from_tree;

        return $this->resolve_start_date_from_attributes($product_id);
    }

    private function scan_start_date_in_tree($value, string $current_key = ''): string
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $next_key = is_string($k) ? $k : $current_key;
                $found = $this->scan_start_date_in_tree($v, $next_key);
                if ($found !== '') return $found;
            }
            return '';
        }

        if (!is_scalar($value)) return '';
        if (!$this->is_start_date_hint_key($current_key)) return '';

        return $this->format_course_date_value($value);
    }

    private function is_start_date_hint_key(string $key): bool
    {
        if ($key === '') return false;
        $normalized = strtolower(function_exists('remove_accents') ? remove_accents($key) : $key);
        foreach (['start', 'date_start', 'opening', 'ngay', 'khai_giang', 'bat_dau'] as $hint) {
            if (strpos($normalized, $hint) !== false) return true;
        }
        return false;
    }

    private function resolve_start_date_from_attributes(int $product_id): string
    {
        $product = wc_get_product($product_id);
        if (!$product) return '';

        foreach ($product->get_attributes() as $attribute) {
            if (!is_a($attribute, \WC_Product_Attribute::class)) continue;

            $name = (string) $attribute->get_name();
            $label = (string) wc_attribute_label($name);
            $hint = strtolower(function_exists('remove_accents') ? remove_accents($name . ' ' . $label) : $name . ' ' . $label);

            $is_date_attr =
                strpos($hint, 'start') !== false
                || strpos($hint, 'date') !== false
                || strpos($hint, 'ngay') !== false
                || strpos($hint, 'khai') !== false
                || strpos($hint, 'bat dau') !== false
                || strpos($hint, 'bat-dau') !== false
                || strpos($hint, 'bat_dau') !== false;

            if (!$is_date_attr) continue;

            if ($attribute->is_taxonomy()) {
                foreach ((array) $attribute->get_options() as $term_id) {
                    $term = get_term((int) $term_id);
                    if ($term && !is_wp_error($term)) {
                        $formatted = $this->format_course_date_value($term->name);
                        if ($formatted !== '') return $formatted;
                    }
                }
                continue;
            }

            foreach ((array) $attribute->get_options() as $option) {
                $formatted = $this->format_course_date_value($option);
                if ($formatted !== '') return $formatted;
            }
        }

        return '';
    }

    private function format_course_date_value($raw): string
    {
        if (is_array($raw)) {
            foreach (['date', 'value', 'start_date', 'label'] as $sub) {
                if (isset($raw[$sub])) return $this->format_course_date_value($raw[$sub]);
            }
            return '';
        }
        return $this->format_course_date((string) $raw);
    }

    private function format_course_date(string $raw): string
    {
        $value = trim($raw);
        if ($value === '') return '';

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) return $value;

        if (preg_match('/^\d{8}$/', $value)) {
            $dt = \DateTime::createFromFormat('Ymd', $value);
            return $dt ? $dt->format('d/m/Y') : $value;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $dt = \DateTime::createFromFormat('Y-m-d', $value);
            return $dt ? $dt->format('d/m/Y') : $value;
        }

        $ts = strtotime($value);
        return $ts !== false ? wp_date('d/m/Y', $ts) : $value;
    }

    private function get_primary_term(int $post_id, string $taxonomy): array
    {
        $terms = get_the_terms($post_id, $taxonomy);
        if (empty($terms) || is_wp_error($terms)) {
            return ['name' => '', 'url' => ''];
        }

        $t = $terms[0];
        if (!is_object($t) || empty($t->term_id)) {
            return ['name' => '', 'url' => ''];
        }

        $url = get_term_link($t, $taxonomy);
        if (is_wp_error($url)) $url = '';

        return [
            'name' => (string) ($t->name ?? ''),
            'url'  => (string) $url,
        ];
    }
}
