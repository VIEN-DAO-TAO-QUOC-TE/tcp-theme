<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseQuota
{
    use Singleton;

    /**
     * Statuses tính là "đã dùng suất".
     * Mặc định: processing + completed (paid orders).
     * Có thể filter qua hook tcp/course_quota_count_statuses
     */
    private array $count_statuses = ['processing', 'completed'];

    /**
     * TTL cache (seconds).
     */
    private int $cache_ttl = 10 * 60; // 10 phút

    protected function init(): void
    {
        $this->count_statuses = apply_filters('tcp/course_quota_count_statuses', $this->count_statuses);
        $this->cache_ttl      = (int) apply_filters('tcp/course_quota_cache_ttl', $this->cache_ttl);

        add_action('woocommerce_order_status_changed', [$this, 'on_order_status_changed'], 10, 4);
        add_action('woocommerce_saved_order_items',    [$this, 'on_order_items_saved'],    10, 2);
        // add_action('woocommerce_shop_loop_item_title', [$this, 'hook_output_loop_pill'], 15);
    }

    /**
     * Public API: lấy quota info để render UI
     */
    public function get(int $product_id): array
    {
        $product_id = absint($product_id);
        if (!$product_id) {
            return $this->empty();
        }

        // ACF fields
        $enabled  = (bool) $this->acf_get($product_id, 'tcp_course_promo_enabled', false);
        $total    = (int)  $this->acf_get($product_id, 'tcp_course_promo_quota_total', 0);
        $endAt    = (string) $this->acf_get($product_id, 'tcp_course_promo_end_at', '');
        $labelTpl = (string) $this->acf_get($product_id, 'tcp_course_promo_label', 'Chỉ còn {remaining} suất');

        if (!$enabled || $total <= 0) {
            return $this->empty([
                'enabled'        => $enabled,
                'total'          => max(0, $total),
                'end_at'         => $endAt,
                'label_template' => $labelTpl,
            ]);
        }

        if ($endAt && $this->is_expired($endAt)) {
            return $this->empty([
                'enabled'        => true,
                'total'          => $total,
                'end_at'         => $endAt,
                'label_template' => $labelTpl,
                'is_active'      => false,
            ]);
        }

        $used = $this->count_used_units($product_id);
        $remaining = max(0, $total - $used);

        $isActive = $remaining > 0;

        $labelText = $this->render_label($labelTpl, [
            'remaining' => $remaining,
            'used'      => $used,
            'total'     => $total,
        ]);

        return [
            'enabled'        => true,
            'is_active'      => $isActive,
            'product_id'     => $product_id,
            'used'           => $used,
            'total'          => $total,
            'remaining'      => $remaining,
            'end_at'         => $endAt,
            'label_template' => $labelTpl,
            'label_text'     => $labelText,
        ];
    }

    /**
     * Count "used seats" bằng lookup tables của Woo (nhanh).
     */
    public function count_used_units(int $product_id): int
    {
        $product_id = absint($product_id);
        if (!$product_id) return 0;


        

        $cacheKey = $this->cache_key($product_id);
        $cached = get_transient($cacheKey);

        if ($cached !== false) return (int) $cached;

       

        // Nếu lookup table không tồn tại (hiếm) -> fallback
        if (!$this->has_lookup_tables()) {
            $used = $this->fallback_count_used_units($product_id);
            set_transient($cacheKey, $used, $this->cache_ttl);
            return $used;
        }

        global $wpdb;

        


        $opl = $wpdb->prefix . 'wc_order_product_lookup';
        $os  = $wpdb->prefix . 'wc_order_stats';

        $statuses = $this->normalize_statuses($this->count_statuses);
        if (empty($statuses)) {
            set_transient($cacheKey, 0, $this->cache_ttl);
            return 0;
        }

        // Build IN (%s, %s, ...)
        $placeholders = implode(',', array_fill(0, count($statuses), '%s'));

        /**
         * NOTE:
         * - wc_order_product_lookup.product_id: thường là product_id (parent)
         * - wc_order_product_lookup.variation_id: variation id (nếu có)
         *  => Count theo product_id OR variation_id = $product_id để cover cả trường hợp truyền variation id.
         */
        $sql = "
            SELECT COALESCE(SUM(opl.product_qty), 0)
            FROM {$opl} opl
            INNER JOIN {$os} os ON os.order_id = opl.order_id
            WHERE (opl.product_id = %d OR opl.variation_id = %d)
              AND os.status IN ({$placeholders})
        ";

        $params = array_merge([$product_id, $product_id], $statuses);

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $used = (int) $wpdb->get_var($wpdb->prepare($sql, $params));
       
        set_transient($cacheKey, $used, $this->cache_ttl);

        return $used;
    }

    /**
     * Clear cache khi status đổi.
     */
    public function on_order_status_changed(int $order_id, string $from, string $to, $order): void
    {
        $order = $order instanceof \WC_Order ? $order : wc_get_order($order_id);
        if (!$order) return;

        $this->clear_cache_for_order_products($order);
    }

    /**
     * Clear cache khi items save.
     */
    public function on_order_items_saved(int $order_id, $items): void
    {
        $order = wc_get_order($order_id);
        if (!$order) return;

        $this->clear_cache_for_order_products($order);
    }

    private function clear_cache_for_order_products(\WC_Order $order): void
    {
        $productIds = [];

        foreach ($order->get_items('line_item') as $item) {
            $pid = (int) $item->get_product_id();
            $vid = (int) $item->get_variation_id();

            if ($pid) $productIds[] = $pid;
            if ($vid) $productIds[] = $vid;
        }

        $productIds = array_values(array_unique(array_filter($productIds)));

        foreach ($productIds as $pid) {
            delete_transient($this->cache_key($pid));
        }
    }

    /**
     * Label template: {remaining} {used} {total}
     */
    public function render_label(string $template, array $vars): string
    {
        $replacements = [
            '{remaining}' => (string) ($vars['remaining'] ?? ''),
            '{used}'      => (string) ($vars['used'] ?? ''),
            '{total}'     => (string) ($vars['total'] ?? ''),
        ];

        return trim(strtr($template, $replacements));
    }

    /**
     * ===== Internals =====
     */
    private function cache_key(int $product_id): string
    {
        $statuses = implode(',', $this->normalize_statuses($this->count_statuses));
        $hash = md5($statuses ?: 'none');

        return 'tcp_course_quota_used_' . absint($product_id) . '_' . $hash;
    }

    private function normalize_statuses(array $statuses): array
    {
        $statuses = array_values(array_filter(array_map('strval', $statuses)));

        return array_map(function ($st) {
            $st = str_replace('wc-', '', $st);
            return 'wc-' . $st;
        }, $statuses);
    }

    private function is_expired(string $endAt): bool
    {
        $ts = strtotime($endAt);
        if (!$ts) return false;
        return $ts < time();
    }

    private function empty(array $override = []): array
    {
        $base = [
            'enabled'        => false,
            'is_active'      => false,
            'product_id'     => 0,
            'used'           => 0,
            'total'          => 0,
            'remaining'      => 0,
            'end_at'         => '',
            'label_template' => '',
            'label_text'     => '',
        ];

        return array_merge($base, $override);
    }

    private function acf_get(int $post_id, string $field_name, $default = null)
    {
        if (function_exists('get_field')) {
            $val = get_field($field_name, $post_id);
            return ($val !== null && $val !== false && $val !== '') ? $val : $default;
        }
        return $default;
    }

    /**
     * Check lookup tables existence (cached static).
     */
    private function has_lookup_tables(): bool
    {
        static $ok = null;
        if ($ok !== null) return $ok;

        global $wpdb;
        $opl = $wpdb->prefix . 'wc_order_product_lookup';
        $os  = $wpdb->prefix . 'wc_order_stats';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $t1 = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $opl));
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $t2 = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $os));

        $ok = (!empty($t1) && !empty($t2));
        return $ok;
    }

    /**
     * Fallback chậm (hiếm khi dùng) nếu lookup tables missing.
     */
    private function fallback_count_used_units(int $product_id): int
    {
        $orderIds = wc_get_orders([
            'status' => $this->normalize_statuses($this->count_statuses),
            'limit'  => -1,
            'return' => 'ids',
        ]);

        if (empty($orderIds)) return 0;

        $totalQty = 0;

        foreach ($orderIds as $orderId) {
            $order = wc_get_order($orderId);
            if (!$order) continue;

            foreach ($order->get_items('line_item') as $item) {
                $pid = (int) $item->get_product_id();
                $vid = (int) $item->get_variation_id();

                if ($pid === $product_id || $vid === $product_id) {
                    $totalQty += max(0, (int) $item->get_quantity());
                }
            }
        }

        return (int) $totalQty;
    }
    public function get_progress_percent(int $product_id, string $mode = 'remaining'): int
    {
        $product_id = absint($product_id);
        if (!$product_id) return 0;

        $quota = $this->get($product_id);

        if (empty($quota['enabled']) || empty($quota['total'])) {
            return 0;
        }

        $total = (int) $quota['total'];
        $remaining = (int) $quota['remaining'];
        $used = (int) $quota['used'];

        if ($total <= 0) return 0;

        // mode:
        // - 'remaining': bar thể hiện suất còn lại (càng ít càng ngắn)
        // - 'used': bar thể hiện suất đã dùng (càng mua càng đầy)
        if ($mode === 'used') {
            $pct = (int) round(($used / $total) * 100);
        } else {
            $pct = (int) round(($remaining / $total) * 100);
        }

        return max(0, min(100, $pct));
    }

    /**
     * Render quota pill (progress) dùng cho loop card.
     * Output HTML có --quota-pct để SCSS xử lý.
     */
    public function render_loop_pill(int $product_id, array $args = []): string
    {
        $product_id = absint($product_id);
        if (!$product_id) return '';

        $defaults = [
            'mode' => 'remaining', // 'remaining' | 'used'
            'class' => '',
            'show_when_inactive' => false, // nếu hết suất thì có hiển thị "Hết suất" hay ẩn luôn
            'inactive_text' => 'Hết suất ưu đãi',
        ];
        $args = wp_parse_args($args, $defaults);

        $quota = $this->get($product_id);

        // echo "<pre>";
        //     var_dump($quota);
        // echo "</pre>";

        if (empty($quota['enabled'])) return '';

        if (empty($quota['is_active'])) {
            if (empty($args['show_when_inactive'])) return '';
            $pct = 0;
            $text = (string) $args['inactive_text'];
        } else {
            $pct = $this->get_progress_percent($product_id, (string) $args['mode']);
            $text = (string) ($quota['label_text'] ?? '');
        }

        $class = 'c-courseQuota';
        if (!empty($args['class'])) {
            $class .= ' ' . sanitize_html_class($args['class']);
        }
        $fire_icon = get_stylesheet_directory_uri() . '/assets/images/icons/fire.gif';

        return sprintf(
            '<div class="%s" style="--quota-pct:%d%%" data-quota="%d/%d">' .
                '<span class="c-courseQuota__icon" aria-hidden="true"><img
                                    class="c-course-sidebar__noteIconImg"
                                    src="%s"
                                    alt=""
                                    loading="lazy"
                                    decoding="async" /></span>' .
                '<span class="c-courseQuota__text">%s</span>' .
                '</div>',
            esc_attr($class),
            (int) $pct,
            (int) ($quota['remaining'] ?? 0),
            (int) ($quota['total'] ?? 0),
            esc_url($fire_icon),
            esc_html($text)
        );
    }

    /**
     * Echo helper (tiện dùng trong template/hook).
     */
    public function output_loop_pill(int $product_id, array $args = []): void
    {
        echo $this->render_loop_pill($product_id, $args);
    }

    public function hook_output_loop_pill(): void
    {
        // Tránh chạy ở trang single product
        if (is_product()) return;

        $product_id = get_the_ID();
        if (!$product_id) return;

        echo $this->render_loop_pill($product_id, [
            'mode' => 'remaining',
        ]);
    }
}
