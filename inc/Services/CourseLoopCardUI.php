<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseLoopCardUI
{
    use Singleton;

    protected function init(): void
    {
        /**
         * 1) Move excerpt: remove khỏi flatsome_product_box_after
         *    và add vào woocommerce_shop_loop_item_title để hiển thị ngay sau title
         */
        remove_action('flatsome_product_box_after', 'flatsome_woocommerce_shop_loop_excerpt', 20);
        add_action('woocommerce_shop_loop_item_title', [$this, 'woocommerce_shop_loop_excerpt'], 15);

        /**
         * 2) Chèn quota pill sau excerpt (vẫn nằm trong title-wrapper)
         *    Priority > 15 để nằm sau excerpt
         */
        // add_action('woocommerce_shop_loop_item_title', [$this, 'output_quota_pill'], 20);
    }

    /**
     * Validate theo rule: chỉ "mua được" mới hiển thị quota pill (và các UI mua khác nếu cần).
     */
    public function can_render_purchase_ui(\WC_Product $product): bool
    {
        if (!$product->is_purchasable() || !$product->is_in_stock()) {
            return false;
        }

        if ($product->is_type(['variable', 'grouped', 'external'])) {
            return false;
        }

        return true;
    }

    /**
     * Output quota pill (progress) trong loop card.
     * - Chỉ output khi "mua được" theo validate.
     * - Quota lấy từ CourseQuota service (Option B).
     */
    public function output_quota_pill(): void
    {
        // Tránh chạy ở single product (nếu theme reuse hook)
        if (is_product()) return;

        global $product;

        if (!$product || !is_a($product, \WC_Product::class)) return;

        if (!$this->can_render_purchase_ui($product)) {
            return;
        }

        // Render pill: sẽ tự ẩn nếu promo không enabled hoặc không active (theo logic render_loop_pill)
        echo CourseQuota::instance()->render_loop_pill($product->get_id(), [
            'mode' => 'remaining',
            // 'show_when_inactive' => false, // mặc định false
        ]);
    }
    public function woocommerce_shop_loop_excerpt()
    {
        global $product;


        if ($this->can_render_purchase_ui($product)) {
            return;
        }
        ob_start();
?>
        <p class="box-excerpt is-small">
            <?php echo get_the_excerpt(); ?>
        </p>
<?php
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
}
