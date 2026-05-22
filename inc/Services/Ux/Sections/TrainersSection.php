<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class TrainersSection
{
    public static function render(array $data): string
    {
        $trainersEyebrow = (string) ($data['trainersEyebrow'] ?? '');
        $trainersTitle = (string) ($data['trainersTitle'] ?? '');
        $trainersHighlight = (string) ($data['trainersHighlight'] ?? '');
        $trainersBtnText = (string) ($data['trainersBtnText'] ?? '');
        $trainersBtnUrl = (string) ($data['trainersBtnUrl'] ?? '#');
        $trainersIds = (string) ($data['trainersIds'] ?? '');
        $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];

        $out = '';
        $out .= '[section label="home-trainers" class="home-trainers c-section c-section-header"]';
        $out .= '[row class="home-trainers__row home-trainers__row--header c-section-header__row"]';
        $out .= '[col span="6" span__sm="12" class="home-trainers__col home-trainers__col--intro c-section-header__col"]';
        $out .= '[ux_text class="c-section-header__eyebrow"]' . esc_html($trainersEyebrow) . '[/ux_text]';
        $out .= '[ux_text class="c-section-header__title"]<h2>' . esc_html($trainersTitle) . ' <span style="color: var(--orange-600, #EA580C);">' . esc_html($trainersHighlight) . '</span></h2>[/ux_text]';
        $out .= '[/col]';
        $out .= '[col span="6" span__sm="12" class="home-trainers__col home-trainers__col--stats"][row_inner class="home-trainers__stats"]';
        foreach ($stats as $stat) {
            $out .= '[col_inner span="4" span__sm="4" class="home-trainers__stat"]';
            $out .= '<p><strong>' . esc_html((string) ($stat['value'] ?? '')) . '</strong></p><p>' . esc_html((string) ($stat['label'] ?? '')) . '</p>';
            $out .= '[/col_inner]';
        }
        $out .= '[/row_inner][/col]';
        $out .= '[col span__sm="12" class="home-trainers__col home-trainers__col--cta"]';
        $out .= '[button text="' . esc_attr($trainersBtnText) . '" color="white" radius="10" icon="icon-arrow-right" link="' . esc_attr($trainersBtnUrl) . '"]';
        $out .= '[/col][/row]';
        $out .= '[ux_html]<style>
            .home-trainers .sherpa-card--trainer{background:#fff;border-radius:12px;overflow:hidden;height:100%;box-shadow:0 4px 15px rgba(0,0,0,.05)}
            .home-trainers .sherpa-card__link{display:block;color:inherit;text-decoration:none}
            .home-trainers .sherpa-card--trainer .card-image img{width:100%;height:220px;object-fit:cover;display:block}
            .home-trainers .sherpa-card--trainer .card-body{padding:14px 14px 16px;background:#eceef3}
            .home-trainers .sherpa-card--trainer .member-name{font-size:26px;font-weight:700;margin:0 0 5px;color:#1f2737;line-height:1.18}
            .home-trainers .sherpa-card--trainer .member-pos{font-size:12px;color:#888;line-height:1.4;text-transform:none;display:block}
            @media (max-width:1024px){.home-trainers .sherpa-card--trainer .member-name{font-size:20px}}
            @media (max-width:849px){.home-trainers .sherpa-card--trainer .card-image img{height:340px}.home-trainers .sherpa-card--trainer .member-name{font-size:30px}}
            @media (max-width:375px){.home-trainers .sherpa-card--trainer .member-name{font-size:24px}}
        </style>[/ux_html]';
        $out .= '[tcp_trainers trainer_ids="' . esc_attr($trainersIds) . '" card_template="about"]';
        $out .= '[row class="home-trainers__cta-mobile-row"][col span__sm="12" class="home-trainers__cta-mobile-col"]';
        $out .= '[button text="' . esc_attr($trainersBtnText) . '" color="white" radius="10" icon="icon-arrow-right" link="' . esc_attr($trainersBtnUrl) . '"]';
        $out .= '[/col][/row]';
        $out .= '[/section]';

        return $out;
    }
}
