<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class ContactCtaSection
{
    public static function render(array $data): string
    {
        $contactImageId = (int) ($data['contactImageId'] ?? 0);
        $contactEyebrow = (string) ($data['contactEyebrow'] ?? '');
        $contactTitle = (string) ($data['contactTitle'] ?? '');
        $contactFormShortcode = (string) ($data['contactFormShortcode'] ?? '');

        // Allow only <br> from CMS title, and keep line breaks from plain text input.
        $contactTitle = wp_kses($contactTitle, ['br' => []]);
        $contactTitle = preg_replace('/\R/u', "<br />", $contactTitle) ?? $contactTitle;

        $out = '';
        $out .= '<style>.c-contact-cta__title-text br{display:none;}@media (max-width:1024px){.c-contact-cta__title-text br{display:block;}}</style>';
        $out .= '[section label="c-contact-cta" class="c-contact-cta c-section-header"]';
        $out .= '[row style="collapse" class="c-contact-cta__container"][col span__sm="12"]';
        $out .= '[row_inner v_align="equal" class="c-contact-cta__card"]';
        $out .= '[col_inner span="8" span__sm="12" span__md="5" class="c-contact-cta__media"]';
        if ($contactImageId > 0) {
            $out .= '[ux_image id="' . $contactImageId . '" image_size="original"]';
        }
        $out .= '[/col_inner]';
        $out .= '[col_inner span="4" span__sm="12" span__md="4" class="c-contact-cta__content"]';
        $out .= '[row_inner_1 style="collapse" class="c-contact-cta__content-inner"][col_inner_1 span__sm="12"]';
        $out .= '[row_inner_2 class="c-section-header__row"][col_inner_2 span__sm="12" align="center" class="c-section-header__col"]';
        $out .= '[ux_text class="c-section-header__eyebrow"]<span style="color: #4f46e5;">' . esc_html($contactEyebrow) . '</span>[/ux_text]';
        $out .= '[ux_text class="c-section-header__title"]<h2 class="c-contact-cta__title-text">' . $contactTitle . '</h2>[/ux_text]';
        $out .= '[/col_inner_2][/row_inner_2]';
        $out .= '[row_inner_2 class="c-contact-cta__form"][col_inner_2 span__sm="12"]';
        $out .= $contactFormShortcode;
        $out .= '[/col_inner_2][/row_inner_2]';
        $out .= '[/col_inner_1][/row_inner_1][/col_inner][/row_inner][/col][/row][/section]';

        return $out;
    }
}
