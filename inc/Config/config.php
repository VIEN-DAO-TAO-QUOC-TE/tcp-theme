<?php
defined('ABSPATH') || exit;

// Nơi để define constants, flags, version...
if (!defined('TCP_THEME_VERSION')) {
    define('TCP_THEME_VERSION', '0.1.0');
}

define('TCP_THEME_PATH', wp_normalize_path(get_stylesheet_directory() . DIRECTORY_SEPARATOR));
define('TCP_THEME_URI', get_stylesheet_directory_uri() . '/');

define('TCP_UX_BUILDER', wp_normalize_path(TCP_THEME_PATH . 'inc/src/ux'));
define('FLATSOME_UX_BUILDER', wp_normalize_path(get_template_directory() . '/inc/builder/shortcodes'));
