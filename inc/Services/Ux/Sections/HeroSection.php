<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class HeroSection
{
    public static function render(array $data): string
    {
        $heroTitle = (string) ($data['heroTitle'] ?? '');
        $heroHighlight = (string) ($data['heroHighlight'] ?? '');
        $heroCardsTabletDesktop = is_array($data['heroCardsTabletDesktop'] ?? null) ? $data['heroCardsTabletDesktop'] : [];
        $heroCardsMobile = is_array($data['heroCardsMobile'] ?? null) ? $data['heroCardsMobile'] : [];
        $heroSlidesTabletDesktop = is_array($data['heroSlidesTabletDesktop'] ?? null) ? $data['heroSlidesTabletDesktop'] : [];
        $heroSlidesMobile = is_array($data['heroSlidesMobile'] ?? null) ? $data['heroSlidesMobile'] : [];

        $out = '';
        $out .= '[section label="learning-platform" bg_color="rgb(248, 250, 252)" class="learning-platform"]';
        $out .= '[row class="learning-platform__row learning-platform__row--title"]';
        $out .= '[col span__sm="12" class="learning-platform__col learning-platform__col--title"]';
        $out .= '<h1>' . esc_html($heroTitle) . '<br /><span style="color: #ea580c;">' . esc_html($heroHighlight) . '</span></h1>';
        $out .= '[/col][/row]';

        if (!empty($heroCardsTabletDesktop) || !empty($heroCardsMobile)) {
            $heroCardCss = '';

            $out .= '[row class="learning-platform__row learning-platform__row--hero"]';
            $out .= '[col span__sm="12" class="learning-platform__col learning-platform__col--hero" visibility="hide-for-small"]';
            $out .= '[row_inner_1 v_align="equal" class="learning-platform__hero-cards js-slider-nav js-slider-nav_2-1-1 js-slider-loop"]';
            foreach ($heroCardsTabletDesktop as $idx => $card) {
                $image = (int) ($card['image'] ?? 0);
                if ($image <= 0) {
                    continue;
                }

                $dynamicClass = '';
                $bgImageUrl = (string) ($card['bg_image_url'] ?? '');
                if ($bgImageUrl !== '') {
                    $dynamicClass = ' learning-platform__hero-card--d' . (int) $idx;
                    $heroCardCss .= '.learning-platform__hero-card--d' . (int) $idx . '{--tcp-card-bg:url("' . esc_url_raw($bgImageUrl) . '") center/cover no-repeat;}';
                }

                $out .= '[col_inner_1 span="4" span__sm="12" class="learning-platform__hero-card-col"]';
                $out .= '[ux_image_box style="overlay" img="' . $image . '" text_pos="top" text_align="left" class="c-profile-card learning-platform__hero-card' . esc_attr($dynamicClass) . '"]';
                if (!empty($card['title'])) {
                    $out .= '<h4>' . esc_html((string) $card['title']) . '</h4>';
                }
                if (!empty($card['cta_text'])) {
                    $out .= '[button text="' . esc_attr((string) $card['cta_text']) . '" color="white" radius="10" link="' . esc_attr((string) ($card['cta_url'] ?? '#')) . '" class="learning-platform__hero-card-btn"]';
                }
                $out .= '[/ux_image_box]';
                $out .= '[/col_inner_1]';
            }
            $out .= '[/row_inner_1]';
            $out .= '[/col]';

            $out .= '[col span__sm="12" class="learning-platform__col learning-platform__col--hero" visibility="show-for-small"]';
            $out .= '[row_inner_1 v_align="equal" class="learning-platform__hero-cards learning-platform__hero-cards--mobile learning-platform__hero-cards--mobile-single js-slider-nav js-slider-nav_1-1-1 js-slider-loop"]';
            foreach ($heroCardsMobile as $idx => $card) {
                $image = (int) ($card['image'] ?? 0);
                if ($image <= 0) {
                    continue;
                }

                $dynamicClass = '';
                $bgImageUrl = (string) ($card['bg_image_url'] ?? '');
                if ($bgImageUrl !== '') {
                    $dynamicClass = ' learning-platform__hero-card--m' . (int) $idx;
                    $heroCardCss .= '.learning-platform__hero-card--m' . (int) $idx . '{--tcp-card-bg:url("' . esc_url_raw($bgImageUrl) . '") center/cover no-repeat;}';
                }

                $out .= '[col_inner_1 span="12" span__sm="12" class="learning-platform__hero-card-col"]';
                $out .= '[ux_image_box style="overlay" img="' . $image . '" text_pos="top" text_align="left" class="c-profile-card learning-platform__hero-card' . esc_attr($dynamicClass) . '"]';
                if (!empty($card['title'])) {
                    $out .= '<h4>' . esc_html((string) $card['title']) . '</h4>';
                }
                if (!empty($card['cta_text'])) {
                    $out .= '[button text="' . esc_attr((string) $card['cta_text']) . '" color="white" radius="10" link="' . esc_attr((string) ($card['cta_url'] ?? '#')) . '" class="learning-platform__hero-card-btn"]';
                }
                $out .= '[/ux_image_box]';
                $out .= '[/col_inner_1]';
            }
            $out .= '[/row_inner_1]';
            $out .= '[/col]';
            $out .= '[/row]';

            if ($heroCardCss !== '') {
                $out .= '[ux_html]<style>' . $heroCardCss . '</style>[/ux_html]';
            }
        } elseif (!empty($heroSlidesTabletDesktop)) {
            $out .= '[row class="learning-platform__row learning-platform__row--hero"]';
            $out .= '[col span__sm="12" class="learning-platform__col learning-platform__col--hero" visibility="hide-for-small"]';
            $out .= '[ux_gallery ids="' . esc_attr(implode(',', $heroSlidesTabletDesktop)) . '" style="default" lightbox="false" type="slider" width="full-width" columns="2" slider_nav_style="circle" slider_bullets="true" auto_slide="4000" image_size="original" class="learning-platform__gallery"]';
            $out .= '[/col]';
            if (!empty($heroSlidesMobile)) {
                $out .= '[col span__sm="12" class="learning-platform__col learning-platform__col--hero" visibility="show-for-small"]';
                $out .= '[ux_gallery ids="' . esc_attr(implode(',', $heroSlidesMobile)) . '" style="default" lightbox="false" type="slider" width="full-width" columns="2" slider_nav_style="circle" slider_bullets="true" auto_slide="4000" image_size="original" class="learning-platform__gallery"]';
                $out .= '[/col]';
            }
            $out .= '[/row]';
        }

        $out .= '[/section]';
        return $out;
    }
}
