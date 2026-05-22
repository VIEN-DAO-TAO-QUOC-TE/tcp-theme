<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class AboutHeroSection
{
    public static function render(array $data): string
    {
        $heroBgUrl = (string) ($data['heroBgUrl'] ?? '');
        $heroTitle = (string) ($data['heroTitle'] ?? '');
        $heroTitleHighlight = (string) ($data['heroTitleHighlight'] ?? '');
        $heroDesc = (string) ($data['heroDesc'] ?? '');

        $heroStyle = '';
        if ($heroBgUrl !== '') {
            $heroStyle = ' style="background-image:url(' . esc_url($heroBgUrl) . ');"';
        }

        $out = '';
        $out .= '<section class="section-hero"' . $heroStyle . '>';
        $out .= '<div class="container">';
        $out .= '<div class="hero-card shadow-3">';
        $out .= '<h6 class="sub-title">'. get_the_title() .'</h6>';
        $out .= '<h1 class="hero-h1">' . wp_kses_post($heroTitle);
        if ($heroTitleHighlight !== '') {
            $out .= ' <span class="accent-color">' . wp_kses_post($heroTitleHighlight) . '</span>';
        }
        $out .= '</h1>';
        $out .= '<div class="hero-p">' . wp_kses_post($heroDesc) . '</div>';
        if ($heroBgUrl !== '') {
            $out .= '<div class="hero-mobile-image show-for-small">';
            $out .= '<img src="' . esc_url($heroBgUrl) . '" alt="Hero image" loading="lazy" decoding="async">';
            $out .= '</div>';
        }
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</section>';

        return $out;
    }
}
