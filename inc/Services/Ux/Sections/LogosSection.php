<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class LogosSection
{
    public static function render(array $data): string
    {
        $logosTitle = (string) ($data['logosTitle'] ?? '');
        $logoIds = is_array($data['logoIds'] ?? null) ? $data['logoIds'] : [];

        $out = '';
        $out .= '[section label="home-logos" class="home-logos c-section"]';
        $out .= '[row class="c-section-header c-section-header--center"][col span__sm="12" align="center"]';
        $out .= '[ux_text class="c-section-header__eyebrow"]<h2>' . esc_html($logosTitle) . '</h2>[/ux_text]';
        $out .= '[/col][/row]';
        $out .= '[row width="full-width" class="logo-slider"][col span__sm="12" class="logo-slider__swiper"]';
        foreach ($logoIds as $logoId) {
            $out .= '[ux_image id="' . (int) $logoId . '" image_size="original" class="swiper-slide"]';
        }
        $out .= '[/col][/row][/section]';

        return $out;
    }
}
