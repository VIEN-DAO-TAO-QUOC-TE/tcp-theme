<?php

namespace TCP\Theme\Services\Product;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class ProductLayoutUI
{
    use Singleton;

    protected function init(): void
    {
        add_action('init', [$this, 'register']);
    }

    public function register()
    {
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    }
}
