<?php

namespace TCP\Theme\Services\Ux\Sections\About;

defined('ABSPATH') || exit;

final class AboutValuesSection
{
    public static function render(array $data): string
    {
        $values = is_array($data['values'] ?? null) ? $data['values'] : [];
        $giveItems = is_array($data['giveItems'] ?? null) ? $data['giveItems'] : [];

        $label = (string) ($values['label'] ?? '');
        $intro = (string) ($values['intro'] ?? '');
        $highlight = (string) ($values['highlight'] ?? '');
        $outro = (string) ($values['outro'] ?? '');
        $leftTitle = (string) ($values['left_title'] ?? '');
        $leftIcon = (string) ($values['left_icon'] ?? '');
        $leftText = (string) ($values['left_text'] ?? '');
        $rightTitle = (string) ($values['right_title'] ?? '');
        $rightIcon = (string) ($values['right_icon'] ?? '');
        $rightText = (string) ($values['right_text'] ?? '');

        $out = '';
        $out .= '<section class="section-values-give"><div class="container section-values-give__container">';
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
        // Treat $leftIcon as an image URL and output an <img> tag
        $iconHtml = '';
        if (!empty($leftIcon)) {
            $iconHtml = '<img src="' . esc_url($leftIcon) . '" alt="" class="values-icon-img" loading="lazy">';
        }
        $out .= '<h3><span class="values-icon">' . $iconHtml . '</span>' . esc_html($leftTitle) . '</h3>';
        $out .= '<p>' . esc_html($leftText) . '</p>';
        $out .= '</div>';
        $out .= '<div class="values-side">';
        // Treat $rightIcon as an image URL and output an <img> tag
        $rightIconHtml = '';
        if (!empty($rightIcon)) {
            $rightIconHtml = '<img src="' . esc_url($rightIcon) . '" alt="" class="values-icon-img" loading="lazy">';
        }
        $out .= '<h3><span class="values-icon">' . $rightIconHtml . '</span>' . esc_html($rightTitle) . '</h3>';
        $out .= '<p>' . esc_html($rightText) . '</p>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="section-values-give__give hide-for-small"><div class="container">';
        $out .= '<div class="give-row">';
        foreach ($giveItems as $index => $item) {
            $char      = (string) ($item['char'] ?? '');
            $itemLabel = (string) ($item['label'] ?? '');
            $itemText  = (string) ($item['text'] ?? '');

            $out .= '<div class="give-item" tabindex="0">';
            $out .= '<span class="give-char">' . esc_html($char) . '</span>';
            $out .= '<div class="give-content">';
            if ($itemLabel !== '') {
                $out .= '<strong class="give-content__label">' . esc_html($itemLabel) . '</strong>';
            }
            if ($itemText !== '') {
                $out .= '<p class="give-content__text">' . esc_html($itemText) . '</p>';
            }
            $out .= '</div>';
            $out .= '</div>';

            if ($index < count($giveItems) - 1) {
                $out .= '<span class="give-dot">&bull;</span>';
            }
        }
        $out .= '</div>';
        $out .= '</div></div>';

        $out .= '<div class="section-values-give__give-mobile show-for-small">';
        $out .= '<div class="container">';
        foreach ($giveItems as $index => $item) {
            $char = (string) ($item['char'] ?? '');
            $label = (string) ($item['label'] ?? '');
            $text = (string) ($item['text'] ?? '');

            $out .= '<div class="give-m-item">';
            $out .= '<div class="give-m-letter"><h2 class="give-char-m">' . esc_html($char) . '</h2></div>';
            $out .= '<div class="give-m-info">';
            if ($label !== '') {
                $out .= '<strong>' . esc_html($label) . '</strong>';
            }
            if ($text !== '') {
                $out .= '<p>' . esc_html($text) . '</p>';
            }
            $out .= '</div></div>';

            if ($index < count($giveItems) - 1) {
                $out .= '<div class="give-m-divider"><span class="give-dot">&bull;</span></div>';
            }
        }
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</section>';

        return $out;
    }
}
