<?php

namespace TCP\Theme\Services\Ux\Sections\About;

defined('ABSPATH') || exit;

final class AboutBreadcrumbSection
{
    public static function render(): string
    {
        $out = '';
        $out .= '<div class="container">';
        $out .= '<div class="about-breadcrumb">';
        $out .= do_shortcode('[rank_math_breadcrumb]');
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
}
