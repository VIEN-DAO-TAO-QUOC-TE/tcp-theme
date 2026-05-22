<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class AboutBreadcrumbSection
{
    public static function render(): string
    {
        $out = '';
        $out .= '<div class="container">';
        $out .= '<nav class="about-breadcrumb" aria-label="Breadcrumb">';
        $out .= '<a href="' . esc_url(home_url('/')) . '">Home</a>';
        $out .= '<span class="about-breadcrumb__sep">&rsaquo;</span>';
        $out .= '<span>Ve chung toi</span>';
        $out .= '</nav>';
        $out .= '</div>';

        return $out;
    }
}
