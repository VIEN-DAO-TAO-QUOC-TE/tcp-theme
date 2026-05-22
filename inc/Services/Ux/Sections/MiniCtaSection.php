<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class MiniCtaSection
{
    public static function render(array $data): string
    {
        $miniCtaTitle = (string) ($data['miniCtaTitle'] ?? '');
        $miniCtaBtnText = (string) ($data['miniCtaBtnText'] ?? '');
        $miniCtaBtnUrl = (string) ($data['miniCtaBtnUrl'] ?? '#');
        $miniCtaImageId = (int) ($data['miniCtaImageId'] ?? 0);

        $out = '';
        $out .= '[section label="c-cta-banner" class="c-cta-banner"]';
        $out .= '[row style="collapse" class="c-cta-banner__layout"][col span__sm="12" class="c-cta-banner__layout-col"]';
        $out .= '[row_inner v_align="middle" class="c-cta-banner__inner"]';
        $out .= '[col_inner span="5" span__sm="12" class="c-cta-banner__content"]';
        $out .= '<h4>' . esc_html($miniCtaTitle) . '</h4>';
        $out .= '[button text="' . esc_attr($miniCtaBtnText) . '" radius="10" link="' . esc_attr($miniCtaBtnUrl) . '"]';
        $out .= '[/col_inner][/row_inner]';
        if ($miniCtaImageId > 0) {
            $out .= '[ux_image id="' . $miniCtaImageId . '" image_size="original" class="c-cta-banner__media"]';
        }
        $out .= '[/col][/row][/section]';

        return $out;
    }
}
