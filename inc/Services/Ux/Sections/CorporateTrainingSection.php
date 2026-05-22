<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class CorporateTrainingSection
{
    public static function render(array $data): string
    {
        $corporateEyebrow = (string) ($data['corporateEyebrow'] ?? '');
        $corporateTitle = (string) ($data['corporateTitle'] ?? '');
        $corporateTitleHighlight = (string) ($data['corporateTitleHighlight'] ?? '');
        $corporateDesc = (string) ($data['corporateDesc'] ?? '');
        $corporateBtnText = (string) ($data['corporateBtnText'] ?? '');
        $corporateBtnUrl = (string) ($data['corporateBtnUrl'] ?? '#');

        $corporateLeft = is_array($data['corporateLeft'] ?? null) ? $data['corporateLeft'] : [];
        $corporateCenter = is_array($data['corporateCenter'] ?? null) ? $data['corporateCenter'] : [];
        $corporateRight = is_array($data['corporateRight'] ?? null) ? $data['corporateRight'] : [];

        $left1 = (int) ($corporateLeft[0] ?? 0);
        $left2 = (int) ($corporateLeft[1] ?? 0);
        $left3 = (int) ($corporateLeft[2] ?? 0);
        $center1 = (int) ($corporateCenter[0] ?? 0);
        $center2 = (int) ($corporateCenter[1] ?? 0);
        $right1 = (int) ($corporateRight[0] ?? 0);
        $right2 = (int) ($corporateRight[1] ?? 0);
        $right3 = (int) ($corporateRight[2] ?? 0);

        $out = '';
        $out .= '[section class="home-corporate-training c-section"]';
        $out .= '[row v_align="equal" class="home-corporate-training__layout"]';
        $out .= '[col span="4" span__sm="12" class="c-mosaic c-mosaic--left"]';
        $out .= '[row_inner v_align="equal" class="c-mosaic__row"]';
        $out .= '[col_inner span="6" span__sm="9" class="c-mosaic__group c-mosaic__group--stack"]';
        if ($left1 > 0) {
            $out .= '[ux_image id="' . $left1 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        if ($left2 > 0) {
            $out .= '[ux_image id="' . $left2 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        $out .= '[/col_inner]';
        $out .= '[col_inner span="6" span__sm="3" class="c-mosaic__group c-mosaic__group--single"]';
        if ($left3 > 0) {
            $out .= '[ux_image id="' . $left3 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        $out .= '[/col_inner][/row_inner][/col]';

        $out .= '[col span="4" span__sm="12" class="c-mosaic c-mosaic--center c-section-header c-section-header--center"]';
        $out .= '[row_inner class="c-mosaic__row"]';
        $out .= '[col_inner span="6" span__sm="3" class="c-mosaic__group"]';
        if ($center1 > 0) {
            $out .= '[ux_image id="' . $center1 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        $out .= '[/col_inner]';
        $out .= '[col_inner span="6" span__sm="3" class="c-mosaic__group"]';
        if ($center2 > 0) {
            $out .= '[ux_image id="' . $center2 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        $out .= '[/col_inner][/row_inner]';
        $out .= '[row_inner class="c-section-header__row"]';
        $out .= '[col_inner span__sm="12" align="center" class="c-section-header__col"]';
        $out .= '[ux_text class="c-section-header__eyebrow"]' . esc_html($corporateEyebrow) . '[/ux_text]';
        $out .= '[ux_text class="c-section-header__title"]<h2>' . esc_html($corporateTitle) . '<span class="c-section-header__highlight" style="color: var(--orange-600, #EA580C);">' . esc_html($corporateTitleHighlight) . '</span></h2>[/ux_text]';
        $out .= '[ux_text class="c-section-header__desc"]' . esc_html($corporateDesc) . '[/ux_text]';
        $out .= '[button text="' . esc_attr($corporateBtnText) . '" radius="10" link="' . esc_attr($corporateBtnUrl) . '"]';
        $out .= '[/col_inner][/row_inner][/col]';

        $out .= '[col span="4" span__sm="12" class="c-mosaic c-mosaic--right"]';
        $out .= '[row_inner v_align="equal" class="c-mosaic__row"]';
        $out .= '[col_inner span="6" span__sm="3" class="c-mosaic__group c-mosaic__group--single"]';
        if ($right1 > 0) {
            $out .= '[ux_image id="' . $right1 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        $out .= '[/col_inner]';
        $out .= '[col_inner span="6" span__sm="9" class="c-mosaic__group c-mosaic__group--stack"]';
        if ($right2 > 0) {
            $out .= '[ux_image id="' . $right2 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        if ($right3 > 0) {
            $out .= '[ux_image id="' . $right3 . '" image_size="original" lightbox="true" lightbox_image_size="original"]';
        }
        $out .= '[/col_inner][/row_inner][/col]';
        $out .= '[/row][/section]';

        return $out;
    }
}
