<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseData
{
    use Singleton;

    public function get(int $product_id): array
    {
        $cache_key = 'tcp_course_data_' . $product_id;
        $cached = wp_cache_get($cache_key, 'tcp');
        if ($cached !== false) return $cached;

        // Lấy toàn bộ fields của ACF cho product
        $fields = function_exists('get_fields') ? (get_fields($product_id) ?: []) : [];

        wp_cache_set($cache_key, $fields, 'tcp', 60);
        return $fields;
    }

    public function field(array $data, string $key, $default = null)
    {
        return $data[$key] ?? $default;
    }
}
