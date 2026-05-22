<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class CardInfoSection
{
    public static function render(array $data): string
    {
        $cardInfoEyebrow = (string) ($data['cardInfoEyebrow'] ?? '');
        $cardInfoTitle = (string) ($data['cardInfoTitle'] ?? '');
        $cardInfoDesc = (string) ($data['cardInfoDesc'] ?? '');
        $cardInfoBtnText = (string) ($data['cardInfoBtnText'] ?? '');
        $cardInfoBtnUrl = (string) ($data['cardInfoBtnUrl'] ?? '#');
        $cards = is_array($data['cards'] ?? null) ? $data['cards'] : [];

        $out = '';
        $out .= '[section label="home-card-info" class="home-card-info c-section"]';
        $out .= '[row style="collapse" class="home-card-info__layout"][col span__sm="12" class="home-card-info__layout-col"][row_inner class="home-card-info__inner"]';
        $out .= '[col_inner span="3" span__sm="12" class="home-card-info__col home-card-info__col--intro"]';
        $out .= '[ux_text class="c-intro__eyebrow"]' . esc_html($cardInfoEyebrow) . '[/ux_text]';
        $out .= '[ux_text class="c-intro__body"]<h2>' . esc_html($cardInfoTitle) . '</h2>' . nl2br(esc_html($cardInfoDesc)) . '[/ux_text]';
        $out .= '[button text="' . esc_attr($cardInfoBtnText) . '" radius="10" link="' . esc_attr($cardInfoBtnUrl) . '" class="c-intro__btn hidden"]';
        $out .= '[/col_inner]';
        $out .= '[col_inner span="9" span__sm="12" class="home-card-info__col home-card-info__col--cards"]';
        $out .= '[row_inner_1 v_align="equal" class="home-card-info__grid"]';
        foreach ($cards as $card) {
            $out .= '[col_inner_1 span="4" span__sm="12" class="home-card-info__card-col"]';
            $out .= '<div class="c-media-card__wrap">';
            $out .= '[ux_image_box style="overlay" img="' . (int) ($card['image'] ?? 0) . '" text_align="left" class="c-media-card"]';
            $out .= '[ux_text class="c-media-card__title"]<h4>' . esc_html((string) ($card['title'] ?? '')) . '</h4>[/ux_text]';
            $out .= '[ux_text class="c-media-card__desc"]' . esc_html((string) ($card['desc'] ?? '')) . '[/ux_text]';
            $out .= '[ux_text class="c-media-card__meta"]' . esc_html((string) ($card['meta'] ?? '')) . '[/ux_text]';
            $out .= '[/ux_image_box]';
            if (!empty($card['url'])) {
                $out .= '[button text="Xem thêm" radius="10" link="' . esc_attr((string) $card['url']) . '" class="c-media-card__cta"]';
            }
            $out .= '</div>';
            $out .= '[/col_inner_1]';
        }
        $out .= '[/row_inner_1][/col_inner][/row_inner][/col][/row][/section]';

        return $out;
    }
}
