<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class AboutTeamSection
{
    public static function render(array $data): string
    {
        $teamItems = is_array($data['teamItems'] ?? null) ? $data['teamItems'] : [];

        $out = '';
        $out .= '<section class="section-team"><div class="container">';
        $out .= '<div class="section-title text-center">';
        $out .= '<h6 class="blue-color">C-SHERPA</h6>';
        $out .= '<h2>Gap go cac C-Sherpa</h2>';
        $out .= '</div>';
        $out .= '<div class="row row-small team-grid hide-for-small">';
        foreach ($teamItems as $member) {
            $name = (string) ($member['name'] ?? '');
            $pos = (string) ($member['pos'] ?? '');
            $img = (string) ($member['img'] ?? '');

            $out .= '<div class="col medium-3 small-12">';
            $out .= '<div class="sherpa-card">';
            $out .= '<div class="card-image">';
            if ($img !== '') {
                $out .= '<img src="' . esc_url($img) . '" alt="' . esc_attr($name) . '">';
            }
            $out .= '</div>';
            $out .= '<div class="card-body text-left">';
            $out .= '<h4 class="member-name">' . esc_html($name) . '</h4>';
            $out .= '<span class="member-pos">' . esc_html($pos) . '</span>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }
        $out .= '</div>';

        $out .= '<div class="row row-small team-grid-mobile show-for-small">';
        foreach ($teamItems as $member) {
            $name = (string) ($member['name'] ?? '');
            $pos = (string) ($member['pos'] ?? '');
            $img = (string) ($member['img'] ?? '');

            $out .= '<div class="col team-grid-mobile__item">';
            $out .= '<div class="sherpa-card">';
            $out .= '<div class="card-image">';
            if ($img !== '') {
                $out .= '<img src="' . esc_url($img) . '" alt="' . esc_attr($name) . '">';
            }
            $out .= '</div>';
            $out .= '<div class="card-body text-left">';
            $out .= '<h4 class="member-name">' . esc_html($name) . '</h4>';
            $out .= '<span class="member-pos">' . esc_html($pos) . '</span>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }
        $out .= '</div>';
        $out .= '</div></section>';

        return $out;
    }
}
