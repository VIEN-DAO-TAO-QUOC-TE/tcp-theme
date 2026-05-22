<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class CoursesSection
{
    public static function render(array $data): string
    {
        $coursesEyebrow = (string) ($data['coursesEyebrow'] ?? '');
        $coursesTitle = (string) ($data['coursesTitle'] ?? '');
        $coursesHighlight = (string) ($data['coursesHighlight'] ?? '');
        $courseProductIds = (string) ($data['courseProductIds'] ?? '');
        $coursesBtnText = (string) ($data['coursesBtnText'] ?? '');
        $coursesBtnUrl = (string) ($data['coursesBtnUrl'] ?? '#');

        $out = '';
        $out .= '[section label="KHOA HOC" bg_color="rgb(255, 255, 255)" class="home-courses c-section c-section-header"]';
        $out .= '[row class="home-courses__row home-courses__row--header c-section__header c-section-header__row"]';
        $out .= '[col span__sm="12" class="home-courses__col home-courses__col--header c-section-header__col"]';
        $out .= '[ux_text class="c-section-header__eyebrow"]' . esc_html($coursesEyebrow) . '[/ux_text]';
        $out .= '[ux_text class="c-section-header__title"]<h2>' . esc_html($coursesTitle) . ' <span style="color: var(--orange-600, #EA580C);">' . esc_html($coursesHighlight) . '</span></h2>[/ux_text]';
        $out .= '[/col][/row]';
        if ($courseProductIds !== '') {
            $out .= '[ux_products columns__sm="1" columns="4" products="8" ids="' . esc_attr($courseProductIds) . '" class="home-courses__grid c-product-grid"]';
        }
        $out .= '[gap height="16px"]';
        $out .= '[row class="home-courses__cta"][col span__sm="12" align="center"]';
        $out .= '[button text="' . esc_attr($coursesBtnText) . '" color="white" radius="10" icon="icon-arrow-right" link="' . esc_attr($coursesBtnUrl) . '"]';
        $out .= '[/col][/row]';
        $out .= '[/section]';

        return $out;
    }
}
