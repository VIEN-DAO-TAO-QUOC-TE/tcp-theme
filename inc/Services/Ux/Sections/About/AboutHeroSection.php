<?php

namespace TCP\Theme\Services\Ux\Sections\About;

defined('ABSPATH') || exit;

final class AboutHeroSection
{
    public static function render(array $data): string
    {
        $heroBgUrl = self::resolveImageUrl($data['heroBgUrl'] ?? '');
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

    private static function resolveImageUrl($value): string
    {
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return '';
            }

            if (ctype_digit($value)) {
                $url = wp_get_attachment_image_url(absint($value), 'full');
                return is_string($url) ? $url : '';
            }

            return $value;
        }

        if (is_numeric($value)) {
            $url = wp_get_attachment_image_url(absint($value), 'full');
            return is_string($url) ? $url : '';
        }

        if (is_array($value)) {
            if (!empty($value['url']) && is_string($value['url'])) {
                return trim($value['url']);
            }

            if (!empty($value['ID'])) {
                $url = wp_get_attachment_image_url(absint($value['ID']), 'full');
                return is_string($url) ? $url : '';
            }
        }

        return '';
    }
}
