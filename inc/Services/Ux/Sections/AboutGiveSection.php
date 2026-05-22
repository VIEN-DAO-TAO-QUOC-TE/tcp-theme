<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class AboutGiveSection
{
    public static function render(array $data): string
    {
        $giveItems = is_array($data['giveItems'] ?? null) ? $data['giveItems'] : [];

        $out = '';

        $out .= '<section class="section-give hide-for-small"><div class="container text-center"><div class="give-flex">';
        foreach ($giveItems as $index => $item) {
            $char = (string) ($item['char'] ?? '');
            $out .= '<span class="give-char">' . esc_html($char) . '</span>';
            if ($index < count($giveItems) - 1) {
                $out .= '<span class="give-dot">&bull;</span>';
            }
        }
        $out .= '</div></div></section>';

        $out .= '<section class="section-give-mobile show-for-small"><div class="container">';
        foreach ($giveItems as $item) {
            $char = (string) ($item['char'] ?? '');
            $label = (string) ($item['label'] ?? '');
            $text = (string) ($item['text'] ?? '');

            $out .= '<div class="give-m-row">';
            $out .= '<h2 class="give-char-m">' . esc_html($char) . '</h2>';
            $out .= '<div class="give-m-info">';
            if ($label !== '') {
                $out .= '<strong>' . esc_html($label) . '</strong>';
            }
            if ($text !== '') {
                $out .= '<p>' . esc_html($text) . '</p>';
            }
            $out .= '</div></div>';
        }
        $out .= '</div></section>';

        return $out;
    }
}
