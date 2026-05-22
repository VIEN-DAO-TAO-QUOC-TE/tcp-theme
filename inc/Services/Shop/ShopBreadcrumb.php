<?php

namespace TCP\Theme\Services\Shop;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Services\Ux\Sections\About\AboutBreadcrumbSection;

defined('ABSPATH') || exit;

final class ShopBreadcrumb
{
    use Singleton;

    public function init(): void
    {
        // Render before flatsome_category_header (priority 10) so breadcrumb sits above the title.
        add_action('flatsome_after_header', [$this, 'render'], 9);
        add_filter('rank_math/frontend/breadcrumb/items', [$this, 'shorten_shop_label'], 10, 2);
    }

    public function render(): void
    {
        if (!$this->is_target()) {
            return;
        }
        echo AboutBreadcrumbSection::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Shorten the last breadcrumb item to "Khóa học" on the shop page.
     *
     * @param array $items Each item is [text, url].
     * @return array
     */
    public function shorten_shop_label(array $items, $class): array
    {
        if (!function_exists('is_shop') || !is_shop() || is_product_taxonomy()) {
            return $items;
        }
        if (empty($items)) {
            return $items;
        }
        $last_key = array_key_last($items);
        if (is_array($items[$last_key]) && isset($items[$last_key][0])) {
            $items[$last_key][0] = __('Khóa học', 'tcp-theme');
        }
        return $items;
    }

    private function is_target(): bool
    {
        return (function_exists('is_shop') && is_shop())
            || (function_exists('is_product_taxonomy') && is_product_taxonomy());
    }
}
