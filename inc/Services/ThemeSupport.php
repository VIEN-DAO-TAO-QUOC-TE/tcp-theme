<?php

namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class ThemeSupport
{
    use Singleton;

    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('acf/init', [$this, 'register_options_page']);
    }

    public function register_options_page(): void
    {
        if (!function_exists('acf_add_options_page')) return;

        acf_add_options_page([
            'page_title' => __('TCP Theme Options', 'tcp-theme'),
            'menu_title' => __('TCP Options', 'tcp-theme'),
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'edit_posts',
            'redirect'   => false,
        ]);
    }

    public function setup(): void
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('custom-logo', [
            'height' => 64,
            'width' => 240,
            'flex-height' => true,
            'flex-width' => true,
        ]);

        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

        register_nav_menus([
            'primary' => __('Primary Menu', 'tcp-shadcn'),
            'footer'  => __('Footer Menu', 'tcp-shadcn'),
        ]);
    }
}
