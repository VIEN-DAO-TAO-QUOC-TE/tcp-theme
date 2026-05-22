<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseLoopMetaUI
{
    use Singleton;

    /**
     * Presets giống “component presets”
     */
    private const PRESET_DOT = [
        'variant'   => 'dot',
        'separator' => 'dot', // dot | none
        'show_icon' => [
            'rating'   => true,
            'buyers'   => true,
            'duration' => false,
            'lessons'  => false,
        ],
        'suffix' => [
            'buyers'  => ' học viên',
            'lessons' => ' tập',
        ],
    ];

    private const PRESET_FULL_ICON = [
        'variant'   => 'full-icon',
        'separator' => 'none',
        'show_icon' => [
            'rating'   => true,
            'buyers'   => true,
            'duration' => true,
            'lessons'  => true,
        ],
        'suffix' => [
            'buyers'  => ' học viên',
            'lessons' => ' tập',
        ],
    ];

    /**
     * Icon class mapping
     */
    private const ICON_CLASS = [
        'rating'   => 'c-courseMeta__icon--star icon-star',
        'buyers'   => 'c-courseMeta__icon--users icon-users',
        'duration' => 'c-courseMeta__icon--clock icon-timer',
        'lessons'  => 'c-courseMeta__icon--lessons icon-folders',
    ];

    protected function init(): void
    {
        add_action('tcp_course_card_badges', [$this, 'output_badges'], 0);
        add_action('woocommerce_shop_loop_item_meta', [$this, 'output_meta_row'], 10);
    }

    private function get_product(): ?\WC_Product
    {
        global $product;
        return ($product && is_a($product, \WC_Product::class)) ? $product : null;
    }

    private function acf_get(int $post_id, string $field_name, $default = null)
    {
        if (function_exists('get_field')) {
            $val = get_field($field_name, $post_id);
            if ($val !== null && $val !== false && $val !== '') {
                return $val;
            }
        }

        // Fallback cho trường hợp value được lưu ở post meta nhưng chưa có ACF field object.
        $meta_val = get_post_meta($post_id, $field_name, true);
        if ($meta_val !== null && $meta_val !== false && $meta_val !== '') {
            return $meta_val;
        }

        return $default;
    }

    public function output_badges(): void
    {
        $product = $this->get_product();
        if (!$product) return;

        $product_id = $product->get_id();

        $badges = $this->acf_get($product_id, 'tcp_course_badges', []);
        if (empty($badges) || !is_array($badges)) return;

        echo '<div class="c-courseBadges">';

        foreach ($badges as $row) {
            $text = isset($row['tcp_badge_text']) ? trim((string) $row['tcp_badge_text']) : '';
            if ($text === '') continue;

            $variant = isset($row['tcp_badge_variant']) ? (string) $row['tcp_badge_variant'] : 'orange';
            $variant = preg_replace('/[^a-z0-9\-_]/i', '', $variant);

            printf(
                '<span class="c-courseBadge c-courseBadge--%s">%s</span>',
                esc_attr($variant),
                esc_html($text)
            );
        }

        echo '</div>';
    }

    /**
     * Loop default
     */
    public function output_meta_row(): void
    {
        $product = $this->get_product();
        if (!$product) return;

        $product_id = $product->get_id();

        $this->render_course_card_meta($product, $product_id);
    }

    private function render_course_card_meta(\WC_Product $product, int $product_id): void
    {
        $teacher_name   = $this->get_loop_teacher_name($product_id);
        $teacher_prefix = $this->get_loop_teacher_prefix($product_id);
        $start_date = $this->get_loop_start_date($product_id);
        if ($start_date === '') {
            $start_date = wp_date('d/m/Y');
        }

        $avg         = (float) $product->get_average_rating();
        $rating_text = $avg > 0 ? $this->format_vi_decimal($avg, 1) : '0,0';
        $buyers      = $this->get_buyers_count($product, $product_id);

        echo '<div class="c-courseMeta c-courseMeta--card">';

        if ($teacher_name !== '') {
            echo '<div class="c-courseMeta__teacher">' . esc_html( $teacher_name) . '</div>';
        }

        echo '<div class="c-courseMeta__stats">';
        echo '<span class="c-courseMeta__item c-courseMeta__item--rating">';
        echo '  <span class="c-courseMeta__icon c-courseMeta__icon--star icon-star" aria-hidden="true"></span>';
        echo '  <span class="c-courseMeta__value">' . esc_html($rating_text) . '</span>';
        echo '</span>';

        echo '<span class="c-courseMeta__item c-courseMeta__item--buyers">';
        echo '  <span class="c-courseMeta__icon c-courseMeta__icon--users icon-users" aria-hidden="true"></span>';
        echo '  <span class="c-courseMeta__value">' . esc_html__('Số lượng:', 'tcp-theme') . ' ' . esc_html($this->format_vi_int($buyers)) . '</span>';
        echo '</span>';
        echo '</div>';

        if ($start_date !== '') {
            echo '<div class="c-courseMeta__start">';
            echo '  <span class="c-courseMeta__icon c-courseMeta__icon--calendar icon-calendar" aria-hidden="true"></span>';
            echo '  <span class="c-courseMeta__value">' . esc_html__('Ngày bắt đầu:', 'tcp-theme') . ' ' . esc_html($start_date) . '</span>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function get_buyers_count(\WC_Product $product, int $product_id): int
    {
        $students_source = (string) $this->acf_get($product_id, 'tcp_course_students_source', 'total_sales');
        if ($students_source === 'override') {
            return (int) $this->acf_get($product_id, 'tcp_course_students_override', 0);
        }

        return (int) $product->get_total_sales();
    }

    private function get_loop_teacher_prefix(int $product_id): string
    {
        $prefix_keys = [
            'tcp_course_teacher_prefix',
            'tcp_course_teacher_label',
            'tcp_course_sherpa_label',
        ];

        foreach ($prefix_keys as $key) {
            $val = trim((string) $this->acf_get($product_id, $key, ''));
            if ($val !== '') {
                return $val;
            }
        }

        return 'C-Sherpa';
    }

    private function get_loop_teacher_name(int $product_id): string
    {
        $name_keys = [
            'tcp_course_trainer_name',
            'tcp_course_teacher_name',
            'tcp_course_card_teacher_name',
            'tcp_course_instructor_name',
        ];

        foreach ($name_keys as $key) {
            $val = trim((string) $this->acf_get($product_id, $key, ''));
            if ($val !== '') {
                return $val;
            }
        }

        $ref = function_exists('get_field') ? get_field('tcp_course_trainer_ref', $product_id) : null;

        return $this->extract_trainer_name_from_ref($ref);
    }

    private function extract_trainer_name_from_ref($ref): string
    {
        if (is_array($ref) && isset($ref[0])) {
            $ref = $ref[0];
        }

        $trainer_id = 0;

        if (is_object($ref) && isset($ref->ID)) {
            $trainer_id = (int) $ref->ID;
        } elseif (is_numeric($ref)) {
            $trainer_id = (int) $ref;
        }

        if ($trainer_id <= 0) {
            return '';
        }

        return trim((string) get_the_title($trainer_id));
    }

    private function get_loop_start_date(int $product_id): string
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
            $raw = $this->acf_get($product_id, $key, '');
            $formatted = $this->format_course_date($raw);
            if ($formatted !== '') {
                return $formatted;
            }
        }

        // Fallback 1: quet toàn bộ ACF fields lồng nhau theo key gợi ý ngày bắt đầu.
        $from_fields = $this->extract_start_date_from_fields_tree($product_id);
        if ($from_fields !== '') {
            return $from_fields;
        }

        // Fallback 2: nhiều site lưu "ngày bắt đầu/khai giảng" ở product attributes.
        $from_attributes = $this->extract_start_date_from_product_attributes($product_id);
        if ($from_attributes !== '') {
            return $from_attributes;
        }

        return '';
    }

    private function extract_start_date_from_fields_tree(int $product_id): string
    {
        if (!function_exists('get_fields')) {
            return '';
        }

        $fields = get_fields($product_id);
        if (!is_array($fields) || empty($fields)) {
            return '';
        }

        return $this->scan_start_date_in_tree($fields);
    }

    private function scan_start_date_in_tree($data, string $current_key = ''): string
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $next_key = is_string($key) ? $key : $current_key;
                $found = $this->scan_start_date_in_tree($value, $next_key);
                if ($found !== '') {
                    return $found;
                }
            }

            return '';
        }

        if (!is_scalar($data)) {
            return '';
        }

        if (!$this->is_start_date_hint_key($current_key)) {
            return '';
        }

        return $this->format_course_date((string) $data);
    }

    private function is_start_date_hint_key(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        $normalized = strtolower(remove_accents($key));
        $hints = [
            'start',
            'date_start',
            'opening',
            'ngay',
            'khai_giang',
            'bat_dau',
        ];

        foreach ($hints as $hint) {
            if (strpos($normalized, $hint) !== false) {
                return true;
            }
        }

        return false;
    }

    private function extract_start_date_from_product_attributes(int $product_id): string
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return '';
        }

        foreach ($product->get_attributes() as $attribute) {
            if (!is_a($attribute, \WC_Product_Attribute::class)) {
                continue;
            }

            $name  = (string) $attribute->get_name();
            $label = (string) wc_attribute_label($name);
            $hint_source = strtolower(remove_accents($name . ' ' . $label));

            if (
                strpos($hint_source, 'start') === false
                && strpos($hint_source, 'date') === false
                && strpos($hint_source, 'ngay') === false
                && strpos($hint_source, 'khai') === false
                && strpos($hint_source, 'bat dau') === false
                && strpos($hint_source, 'bat-dau') === false
                && strpos($hint_source, 'bat_dau') === false
            ) {
                continue;
            }

            if ($attribute->is_taxonomy()) {
                foreach ((array) $attribute->get_options() as $term_id) {
                    $term = get_term((int) $term_id);
                    if ($term && !is_wp_error($term)) {
                        $formatted = $this->format_course_date((string) $term->name);
                        if ($formatted !== '') {
                            return $formatted;
                        }
                    }
                }

                continue;
            }

            foreach ((array) $attribute->get_options() as $option) {
                $formatted = $this->format_course_date((string) $option);
                if ($formatted !== '') {
                    return $formatted;
                }
            }
        }

        return '';
    }

    private function format_course_date($raw): string
    {
        if (is_array($raw)) {
            if (isset($raw['date'])) {
                return $this->format_course_date($raw['date']);
            }

            if (isset($raw['value'])) {
                return $this->format_course_date($raw['value']);
            }

            if (isset($raw['start_date'])) {
                return $this->format_course_date($raw['start_date']);
            }

            if (isset($raw['label'])) {
                return $this->format_course_date($raw['label']);
            }

            return '';
        }

        $value = trim((string) $raw);
        if ($value === '') {
            return '';
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return $value;
        }

        if (preg_match('/^\d{8}$/', $value)) {
            $dt = \DateTime::createFromFormat('Ymd', $value);
            return $dt ? $dt->format('d/m/Y') : $value;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $dt = \DateTime::createFromFormat('Y-m-d', $value);
            return $dt ? $dt->format('d/m/Y') : $value;
        }

        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return wp_date('d/m/Y', $timestamp);
        }

        return $value;
    }

    /**
     * “Component props”:
     * $props = [
     *   'variant' => 'dot'|'full-icon',
     *   'separator' => 'dot'|'none',
     *   'show_icon' => ['rating'=>bool,'buyers'=>bool,'duration'=>bool,'lessons'=>bool],
     *   'suffix' => ['buyers'=>' học viên','lessons'=>' tập'],
     * ]
     */
    private function render_meta_row_for_product(\WC_Product $product, int $product_id, array $props = []): void
    {
        $props = $this->normalize_props($props);

        $items = $this->build_meta_items($product, $product_id, $props);
        if (empty($items)) return;

        $variant_class = 'c-courseMeta--' . preg_replace('/[^a-z0-9\-_]/i', '', (string) $props['variant']);

        echo '<div class="c-courseMeta ' . esc_attr($variant_class) . '">';

        $first = true;
        foreach ($items as $key => $item) {
            if (!$first && $props['separator'] === 'dot') {
                echo '<span class="c-courseMeta__dot" aria-hidden="true">•</span>';
            }
            $first = false;

            echo $this->render_item($key, $item, $props);
        }

        echo '</div>';
    }

    private function build_meta_items(\WC_Product $product, int $product_id, array $props): array
    {
        // Rating
        $avg = (float) $product->get_average_rating(); // 0 nếu chưa có review
        $rating_text = $avg > 0 ? $this->format_vi_decimal($avg, 1) : '';

        // Buyers source
        $students_source = (string) $this->acf_get($product_id, 'tcp_course_students_source', 'total_sales');
        if ($students_source === 'override') {
            $buyers = (int) $this->acf_get($product_id, 'tcp_course_students_override', 0);
        } else {
            $buyers = (int) $product->get_total_sales();
        }

        // Duration + Lessons
        $duration = (string) $this->acf_get($product_id, 'tcp_course_duration_text', '');
        $lessons  = (int) $this->acf_get($product_id, 'tcp_course_lesson_count', 0);

        $items = [];

        if ($rating_text !== '') {
            $items['rating'] = [
                'value' => $rating_text,
            ];
        }

        if ($buyers > 0) {
            $items['buyers'] = [
                'value' => $this->format_vi_int($buyers) . ($props['suffix']['buyers'] ?? ''),
            ];
        }

        if ($duration !== '') {
            // duration text đã có sẵn kiểu "2 giờ học"
            $items['duration'] = [
                'value' => $duration,
            ];
        }

        if ($lessons > 0) {
            $items['lessons'] = [
                'value' => (string) $lessons . ($props['suffix']['lessons'] ?? ''),
            ];
        }

        return $items;
    }

    private function render_item(string $key, array $item, array $props): string
    {
        $show_icon = !empty($props['show_icon'][$key]);
        $icon_class = self::ICON_CLASS[$key] ?? '';

        $html  = '<span class="c-courseMeta__item c-courseMeta__item--' . esc_attr($key) . '">';

        if ($show_icon && $icon_class !== '') {
            $html .= '<span class="c-courseMeta__icon ' . esc_attr($icon_class) . '" aria-hidden="true"></span>';
        }

        $html .= '<span class="c-courseMeta__value">' . esc_html((string) ($item['value'] ?? '')) . '</span>';
        $html .= '</span>';

        return $html;
    }

    private function normalize_props(array $props): array
    {
        // Cho phép gọi nhanh theo preset name (giống React component variants)
        // Ví dụ: render_compact($p, 0, ['preset' => 'full_icon'])
        if (!empty($props['preset'])) {
            $preset = (string) $props['preset'];
            if ($preset === 'full_icon') {
                $props = array_merge(self::PRESET_FULL_ICON, $props);
            } else {
                $props = array_merge(self::PRESET_DOT, $props);
            }
        }

        $base = self::PRESET_DOT;

        // Merge sâu show_icon + suffix
        $merged = array_merge($base, $props);

        $merged['show_icon'] = array_merge($base['show_icon'], $props['show_icon'] ?? []);
        $merged['suffix']    = array_merge($base['suffix'], $props['suffix'] ?? []);

        $merged['variant']   = $merged['variant'] ?? 'dot';
        $merged['separator'] = $merged['separator'] ?? 'dot';

        return $merged;
    }

    private function format_vi_decimal(float $num, int $decimals = 1): string
    {
        $s = number_format($num, $decimals, '.', '');
        return str_replace('.', ',', $s);
    }

    private function format_vi_int(int $num): string
    {
        return number_format($num, 0, ',', '.');
    }

    /**
     * Dùng trong sidebar/taskbar (không phụ thuộc global $product)
     * - Có thể truyền preset/props để ra đúng UI mong muốn
     */
    public function render_compact(\WC_Product $product, int $product_id = 0, array $props = []): void
    {
        $pid = $product_id > 0 ? $product_id : $product->get_id();
        $this->render_meta_row_for_product($product, $pid, $props ?: self::PRESET_DOT);
    }

    /**
     * Helper convenience: gọi thẳng preset
     */
    public function render_dot(\WC_Product $product, int $product_id = 0): void
    {
        $this->render_compact($product, $product_id, self::PRESET_DOT);
    }

    public function render_full_icon(\WC_Product $product, int $product_id = 0): void
    {
        $this->render_compact($product, $product_id, self::PRESET_FULL_ICON);
    }
}
