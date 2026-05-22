<?php
defined('ABSPATH') || exit;

if (!function_exists('tcp_theme')) {
    function tcp_theme(): \TCP\Theme\Theme
    {
        return \TCP\Theme\Theme::instance();
    }
}

if (!function_exists('tcp_classes')) {
    /**
     * Merge class strings safely.
     * tcp_classes('a', ['b', false, 'c'], 'd') => "a b c d"
     */
    function tcp_classes(...$classes): string
    {
        $out = [];

        $flatten = function ($item) use (&$out, &$flatten) {
            if (is_array($item)) {
                foreach ($item as $sub) $flatten($sub);
                return;
            }
            if (is_string($item) && trim($item) !== '') {
                $out[] = trim($item);
            }
        };

        foreach ($classes as $c) $flatten($c);

        // unique but keep order
        $out = array_values(array_unique($out));

        return implode(' ', $out);
    }
}

if (!function_exists('tcp_view')) {
    /**
     * Render a template part with variables.
     */
    function tcp_view(string $path, array $data = []): void
    {
        $file = get_template_directory() . '/' . ltrim($path, '/');
        if (!is_readable($file)) return;

        extract($data, EXTR_SKIP);
        include $file;
    }
}
