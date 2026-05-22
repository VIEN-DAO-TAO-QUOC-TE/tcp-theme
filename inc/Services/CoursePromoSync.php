<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CoursePromoSync
{
    use Singleton;

    /**
     * Meta backup (để restore sale khi promo active trở lại)
     */
    private string $meta_backup_sale_price   = '_tcp_promo_backup_sale_price';
    private string $meta_backup_date_from    = '_tcp_promo_backup_sale_from';
    private string $meta_backup_date_to      = '_tcp_promo_backup_sale_to';
    private string $meta_backup_created_at   = '_tcp_promo_backup_created_at';

    /**
     * Lock transient để tránh chạy sync liên tục.
     */
    private int $lock_ttl = 30; // seconds

    /**
     * Cron hook name
     */
    private string $cron_hook = 'tcp/course_promo_sync_cron';

    protected function init(): void
    {
        // Sync khi đơn hàng đổi trạng thái / thay đổi line items
        add_action('woocommerce_order_status_changed', [$this, 'on_order_status_changed'], 10, 4);
        add_action('woocommerce_saved_order_items',    [$this, 'on_order_items_saved'],    10, 2);

        // Sync khi product save (admin chỉnh ACF quota/endAt…)
        add_action('save_post_product', [$this, 'on_product_saved'], 20, 2);

        // Cron xử lý promo hết hạn theo thời gian (không có order mới)
        add_action($this->cron_hook, [$this, 'cron_sync_expired_promos']);

        if (!wp_next_scheduled($this->cron_hook)) {
            wp_schedule_event(time() + 120, 'hourly', $this->cron_hook);
        }
    }

    /**
     * Public: Sync một product theo trạng thái quota hiện tại.
     * - Nếu promo inactive => tắt sale
     * - Nếu promo active => restore sale (nếu có backup)
     */
    public function sync_product(int $product_id): void
    {
        $product_id = absint($product_id);
        if (!$product_id) return;

        if ($this->is_locked($product_id)) return;
        $this->lock($product_id);

        // Chỉ sync nếu promo enabled (ACF meta)
        if (!$this->is_promo_enabled($product_id)) {
            return;
        }

        $quota = CourseQuota::instance()->get($product_id);

        // Nếu không enabled hoặc không active => kết thúc promo
        if (empty($quota['enabled']) || empty($quota['is_active'])) {
            $this->end_sale_if_needed($product_id);
            return;
        }

        // Promo active => đảm bảo sale price được bật (nếu có backup)
        $this->restore_sale_if_possible($product_id, $quota);
    }

    /**
     * Kết thúc sale price Woo khi promo inactive.
     * - Lưu backup sale_price + date_from/to (1 lần) để có thể restore.
     * - Clear sale price & sale schedule.
     */
    private function end_sale_if_needed(int $product_id): void
    {
        $product = wc_get_product($product_id);
        if (!$product) return;

        $regular = $product->get_regular_price();
        $sale    = $product->get_sale_price();

        // Nếu không có sale đang chạy thì thôi
        if ($sale === '' || $sale === null) {
            return;
        }

        // Backup sale hiện tại (nếu chưa backup)
        if (!metadata_exists('post', $product_id, $this->meta_backup_sale_price)) {
            update_post_meta($product_id, $this->meta_backup_sale_price, $sale);

            $from = $product->get_date_on_sale_from();
            $to   = $product->get_date_on_sale_to();

            update_post_meta($product_id, $this->meta_backup_date_from, $from ? $from->getTimestamp() : '');
            update_post_meta($product_id, $this->meta_backup_date_to,   $to   ? $to->getTimestamp()   : '');

            update_post_meta($product_id, $this->meta_backup_created_at, time());
        }

        // Clear sale
        $product->set_sale_price('');
        $product->set_date_on_sale_from(null);
        $product->set_date_on_sale_to(null);

        // Đảm bảo _price quay về regular
        $product->set_price($regular);

        $product->save();
    }

    /**
     * Restore sale price nếu promo active trở lại:
     * - Chỉ restore khi product đang không có sale_price và có backup.
     * - Có thể tự set sale_date_to = end_at (ACF) nếu backup không có date_to.
     */
    private function restore_sale_if_possible(int $product_id, array $quota): void
    {
        $product = wc_get_product($product_id);
        if (!$product) return;

        $sale = $product->get_sale_price();
        if ($sale !== '' && $sale !== null) {
            // Đang sale rồi => không đụng
            return;
        }

        $backupSale = get_post_meta($product_id, $this->meta_backup_sale_price, true);
        if ($backupSale === '' || $backupSale === null) {
            // Không có backup => admin chưa set sale price bao giờ, hoặc chưa từng end promo
            return;
        }

        $regular = $product->get_regular_price();

        // Restore sale price
        $product->set_regular_price($regular);
        $product->set_sale_price($backupSale);

        // Restore schedule (nếu có)
        $fromTs = get_post_meta($product_id, $this->meta_backup_date_from, true);
        $toTs   = get_post_meta($product_id, $this->meta_backup_date_to, true);

        $fromTs = is_numeric($fromTs) ? (int) $fromTs : 0;
        $toTs   = is_numeric($toTs)   ? (int) $toTs   : 0;

        if ($fromTs > 0) {
            $product->set_date_on_sale_from(wc_string_to_datetime(gmdate('Y-m-d H:i:s', $fromTs)));
        }

        // Nếu backup không có date_to mà ACF end_at có => set date_to = end_at để Woo schedule auto tắt sale
        if ($toTs > 0) {
            $product->set_date_on_sale_to(wc_string_to_datetime(gmdate('Y-m-d H:i:s', $toTs)));
        } else {
            $endAt = $quota['end_at'] ?? '';
            $endTs = $endAt ? strtotime($endAt) : 0;
            if ($endTs > 0) {
                $product->set_date_on_sale_to(wc_string_to_datetime(gmdate('Y-m-d H:i:s', $endTs)));
            }
        }

        // Set active price to sale
        $product->set_price($backupSale);

        $product->save();
    }

    /**
     * ===== Hooks =====
     */

    public function on_order_status_changed(int $order_id, string $from, string $to, $order): void
    {
        $order = $order instanceof \WC_Order ? $order : wc_get_order($order_id);
        if (!$order) return;

        foreach ($this->get_order_product_ids($order) as $pid) {
            // Sync promo quota cho product có thể bị ảnh hưởng bởi đơn này
            $this->sync_product($pid);
        }
    }

    public function on_order_items_saved(int $order_id, $items): void
    {
        $order = wc_get_order($order_id);
        if (!$order) return;

        foreach ($this->get_order_product_ids($order) as $pid) {
            $this->sync_product($pid);
        }
    }

    public function on_product_saved(int $post_id, \WP_Post $post): void
    {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

        // Khi admin chỉnh quota/end_at… => sync ngay
        $this->sync_product((int) $post_id);

        // Clear used cache của CourseQuota để render đúng ngay lập tức
        // (CourseQuota cache key có hash theo statuses, ta xoá theo prefix bằng cách xoá thẳng key “cơ bản”
        // => đơn giản: gọi count lại sẽ tự tạo key mới; ở đây mình chủ động xoá theo các status hiện tại)
        $this->clear_quota_cache((int) $post_id);
    }

    /**
     * Cron: quét các product promo enabled và nếu đã expired/remaining=0 thì end sale.
     */
    public function cron_sync_expired_promos(): void
    {
        $product_ids = $this->query_promo_enabled_product_ids(200);

        foreach ($product_ids as $pid) {
            $this->sync_product((int) $pid);
        }
    }

    /**
     * ===== Helpers =====
     */

    private function is_promo_enabled(int $product_id): bool
    {
        // ACF true_false lưu "1" / "0" trong postmeta
        $val = get_post_meta($product_id, 'tcp_course_promo_enabled', true);
        return (string) $val === '1';
    }

    private function get_order_product_ids(\WC_Order $order): array
    {
        $ids = [];

        foreach ($order->get_items('line_item') as $item) {
            $pid = (int) $item->get_product_id();
            $vid = (int) $item->get_variation_id();

            if ($pid) $ids[] = $pid;
            if ($vid) $ids[] = $vid;
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private function query_promo_enabled_product_ids(int $limit = 200): array
    {
        $q = new \WP_Query([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => $limit,
            'no_found_rows'  => true,
            'meta_query'     => [
                [
                    'key'   => 'tcp_course_promo_enabled',
                    'value' => '1',
                ],
            ],
        ]);

        return $q->posts ?: [];
    }

    private function is_locked(int $product_id): bool
    {
        return (bool) get_transient($this->lock_key($product_id));
    }

    private function lock(int $product_id): void
    {
        set_transient($this->lock_key($product_id), 1, $this->lock_ttl);
    }

    private function lock_key(int $product_id): string
    {
        return 'tcp_course_promo_sync_lock_' . absint($product_id);
    }

    private function clear_quota_cache(int $product_id): void
    {
        // CourseQuota cache key có hash theo statuses.
        // Cách đơn giản: xoá tất cả transient matching bằng DB là nặng.
        // Ở đây: ta xoá theo các status hiện tại
        $statuses = apply_filters('tcp/course_quota_count_statuses', ['processing', 'completed']);
        $statuses = array_map(function ($st) {
            $st = str_replace('wc-', '', (string) $st);
            return 'wc-' . $st;
        }, $statuses);

        $hash = md5(implode(',', $statuses) ?: 'none');
        delete_transient('tcp_course_quota_used_' . absint($product_id) . '_' . $hash);
    }
}
