<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class BooksSection
{
    public static function render(array $data): string
    {
        $booksEyebrow = (string) ($data['booksEyebrow'] ?? '');
        $booksTitle = (string) ($data['booksTitle'] ?? '');
        $booksHighlight = (string) ($data['booksHighlight'] ?? '');
        $bookProductIds = (string) ($data['bookProductIds'] ?? '');
        $booksBtnText = (string) ($data['booksBtnText'] ?? '');
        $booksBtnUrl = (string) ($data['booksBtnUrl'] ?? '#');

        $out = '';
        $out .= '[section label="SACH KY NANG" bg_color="rgb(255, 255, 255)" class="home-courses home-books c-section c-section-header"]';
        $out .= '[row class="home-courses__row home-courses__row--header c-section__header c-section-header__row"]';
        $out .= '[col span__sm="12" class="home-courses__col home-courses__col--header c-section-header__col"]';
        $out .= '[ux_text class="c-section-header__eyebrow uppercase"]' . esc_html($booksEyebrow) . '[/ux_text]';

        if ($booksHighlight !== '') {
            $out .= '[ux_text class="c-section-header__title"]<h2>' . esc_html($booksTitle) . ' <span style="color: var(--orange-600, #EA580C);">' . esc_html($booksHighlight) . '</span></h2>[/ux_text]';
        } else {
            $out .= '[ux_text class="c-section-header__title"]<h2>' . esc_html($booksTitle) . '</h2>[/ux_text]';
        }

        $out .= '[/col][/row]';
        if ($bookProductIds !== '') {
            $out .= '[ux_products type="row" columns__sm="1" columns="4" products="8" equalize_box="true" ids="' . esc_attr($bookProductIds) . '" class="home-courses__grid c-product-grid has-equal-box-heights"]';
        }
        $out .= '[gap height="16px"]';
        $out .= '[row class="home-courses__cta home-books__cta"][col span__sm="12" align="center"]';
        $out .= '[button text="' . esc_attr($booksBtnText) . '" color="white" radius="10" icon="icon-arrow-right" link="' . esc_attr($booksBtnUrl) . '"]';
        $out .= '[/col][/row]';
        $out .= '[/section]';

        return $out;
    }
}
