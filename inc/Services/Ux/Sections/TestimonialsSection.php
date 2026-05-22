<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class TestimonialsSection
{
    public static function render(array $data): string
    {
        $testimonialsEyebrow = (string) ($data['testimonialsEyebrow'] ?? '');
        $testimonialsTitle = (string) ($data['testimonialsTitle'] ?? '');
        $testimonialsHighlight = (string) ($data['testimonialsHighlight'] ?? '');
        $testimonialsIds = (string) ($data['testimonialsIds'] ?? '');
        $testimonialsPerPage = (int) ($data['testimonialsPerPage'] ?? 6);

        $out = '';
        $out .= '[section label="Testimonials" class="home-testimonials c-section-header"]';
        $out .= '[row class="home-testimonials__header c-section-header__row"][col span__sm="12" class="c-section-header__col"]';
        $out .= '[ux_text class="c-section-header__eyebrow"]' . esc_html($testimonialsEyebrow) . '[/ux_text]';
        $out .= '[ux_text class="c-section-header__title"]<h2><span style="color: var(--orange-600, #EA580C);">' . esc_html($testimonialsTitle) . '</span> ' . esc_html($testimonialsHighlight) . '</h2>[/ux_text]';
        $out .= '[/col][/row]';
        $out .= '[tcp_reviews perpage="' . $testimonialsPerPage . '" review_ids="' . esc_attr($testimonialsIds) . '"]';
        $out .= '[/section]';

        return $out;
    }
}
