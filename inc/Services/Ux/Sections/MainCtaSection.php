<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class MainCtaSection
{
    public static function render(array $data): string
    {
        $mainCtaTitle = (string) ($data['mainCtaTitle'] ?? '');
        $mainCtaDesc = (string) ($data['mainCtaDesc'] ?? '');
        $mainCtaBtn1Text = (string) ($data['mainCtaBtn1Text'] ?? '');
        $mainCtaBtn1Url = (string) ($data['mainCtaBtn1Url'] ?? '#');
        $mainCtaBtn2Text = (string) ($data['mainCtaBtn2Text'] ?? '');
        $mainCtaBtn2Url = (string) ($data['mainCtaBtn2Url'] ?? '#');

        $out = '';
        $out .= '[section label="home-cta" bg_color="rgb(255, 255, 255)" class="home-cta c-section"]';
        $out .= '[row style="collapse" class="home-cta__layout last-reset"][col span__sm="12" class="home-cta__layout-col"]';
        $out .= '[row_inner v_align="middle" class="home-cta__panel home-cta__panel--main"]';
        $out .= '[col_inner span="7" span__sm="12" class="home-cta__panel-col home-cta__panel-col--content"]';
        $out .= '<h4>' . esc_html($mainCtaTitle) . '</h4><p>' . esc_html($mainCtaDesc) . '</p>';
        $out .= '[/col_inner]';
        $out .= '[col_inner span="5" span__sm="12" align="right" class="home-cta__panel-col home-cta__panel-col--actions"]';
        $out .= '[button text="' . esc_attr($mainCtaBtn1Text) . '" color="white" radius="10" link="' . esc_attr($mainCtaBtn1Url) . '"]';
        $out .= '[button text="' . esc_attr($mainCtaBtn2Text) . '" radius="10" link="' . esc_attr($mainCtaBtn2Url) . '"]';
        $out .= '[/col_inner]';
        $out .= '[/row_inner][/col][/row][/section]';

        return $out;
    }
}
