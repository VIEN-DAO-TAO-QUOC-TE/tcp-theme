<?php

namespace TCP\Theme\Services\Ux;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class UxButton
{
    use Singleton;

    protected function init(): void
    {
        add_action('ux_builder_setup', [$this, 'register'], 999);
    }

    public function register(): void
    {
        if (!function_exists('ux_builder_shortcodes')) return;

        $sc = ux_builder_shortcodes()->get('button');
        if (!is_array($sc)) return;

        $iconPath = wp_normalize_path(TCP_THEME_PATH . 'inc/Services/Ux/values/lucide-icons.php');

        $lucideIcons = file_exists($iconPath) ? require $iconPath : null;
        if (!is_array($lucideIcons)) return;

        // options -> icon_options -> options -> icon -> options
        if (
            empty($sc['options'])
            || empty($sc['options']['icon_options'])
            || empty($sc['options']['icon_options']['options'])
            || empty($sc['options']['icon_options']['options']['icon'])
            || !is_array($sc['options']['icon_options']['options']['icon'])
        ) {
            return;
        }

        $sc['options']['icon_options']['options']['icon']['options'] = $lucideIcons;

        // ghi lại schema vào registry
        if (function_exists('remove_ux_builder_shortcode')) {
            remove_ux_builder_shortcode('button');
        }

        if (function_exists('add_ux_builder_shortcode')) {
            add_ux_builder_shortcode('button', $sc);
        }
    }
}
