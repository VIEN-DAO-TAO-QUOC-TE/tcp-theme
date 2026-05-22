<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class AboutValuesSection
{
    public static function render(array $data): string
    {
        $values = is_array($data['values'] ?? null) ? $data['values'] : [];

        $label = (string) ($values['label'] ?? '');
        $intro = (string) ($values['intro'] ?? '');
        $highlight = (string) ($values['highlight'] ?? '');
        $outro = (string) ($values['outro'] ?? '');
        $leftTitle = (string) ($values['left_title'] ?? '');
        $leftText = (string) ($values['left_text'] ?? '');
        $rightTitle = (string) ($values['right_title'] ?? '');
        $rightText = (string) ($values['right_text'] ?? '');

        $out = '';
        $out .= '<section class="section-values"><div class="container">';
        $out .= '<div class="values-grid">';
        $out .= '<div class="values-main">';
        if ($label !== '') {
            $out .= '<p class="values-label">' . esc_html($label) . '</p>';
        }
        $out .= '<h2>' . esc_html($intro) . ' ';
        if ($highlight !== '') {
            $out .= '<span>' . esc_html($highlight) . '</span>';
        }
        $out .= esc_html($outro) . '</h2>';
        $out .= '</div>';
        $out .= '<div class="values-side">';
        $out .= '<h3><span class="values-icon">&#9678;</span>' . esc_html($leftTitle) . '</h3>';
        $out .= '<p>' . esc_html($leftText) . '</p>';
        $out .= '</div>';
        $out .= '<div class="values-side">';
        $out .= '<h3><span class="values-icon">&#9873;</span>' . esc_html($rightTitle) . '</h3>';
        $out .= '<p>' . esc_html($rightText) . '</p>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div></section>';

        return $out;
    }
}
