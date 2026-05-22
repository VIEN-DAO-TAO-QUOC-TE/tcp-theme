<?php

namespace TCP\Theme\Services\Ux\Sections\About;

defined('ABSPATH') || exit;

final class AboutTeamSection
{
    public static function render(array $data): string
    {
        $trainersIds = self::normalizeIds($data['trainersIds'] ?? '');

        $out = '';
        $out .= '<section class="section-team"><div class="container">';
        $out .= '<div class="section-title text-center">';
        $out .= '<h6 class="blue-color">C-SHERPA</h6>';
        $out .= '<h2>Gặp Gỡ các Sherpa</h2>';
        $out .= '</div>';
        $out .= '<div class="">';
        $out .= '[tcp_trainers trainer_ids="' . esc_attr($trainersIds) . '" span="3" span__md="4" span__sm="12" row_class="home-trainers__row home-trainers__row--grid home-trainers__grid about-team-trainers" card_template="about"]';
        $out .= '<div class="about-team-mobile-nav" aria-label="About team pagination">';
        $out .= '<button type="button" class="about-team-mobile-nav__btn" data-role="prev" aria-label="Previous trainer">&#8249;</button>';
        $out .= '<div class="about-team-mobile-nav__count"><span data-role="current">1</span><span class="about-team-mobile-nav__dash">&#8212;</span><span data-role="total">1</span></div>';
        $out .= '<button type="button" class="about-team-mobile-nav__btn" data-role="next" aria-label="Next trainer">&#8250;</button>';
        $out .= '</div>';
        $out .= '<script>(function(){if(window.__tcpAboutTeamNavInit){return;}window.__tcpAboutTeamNavInit=true;var init=function(){var sections=document.querySelectorAll("#custom-about-page .section-team");if(!sections.length){return;}sections.forEach(function(section){var row=section.querySelector(".about-team-trainers");var nav=section.querySelector(".about-team-mobile-nav");if(!row||!nav){return;}var prev=nav.querySelector("[data-role=prev]");var next=nav.querySelector("[data-role=next]");var currentEl=nav.querySelector("[data-role=current]");var totalEl=nav.querySelector("[data-role=total]");var ticking=false;var mobileQuery=window.matchMedia("(max-width: 549px)");var getItems=function(){return Array.prototype.slice.call(row.children).filter(function(item){return item.classList&&item.classList.contains("col");});};var currentIndex=function(items){if(!items.length){return 0;}var left=row.scrollLeft;var nearest=0;var minDiff=Number.MAX_VALUE;items.forEach(function(item,index){var diff=Math.abs(item.offsetLeft-left);if(diff<minDiff){minDiff=diff;nearest=index;}});return nearest;};var scrollToIndex=function(index){var items=getItems();if(!items.length){return;}var targetIndex=Math.max(0,Math.min(index,items.length-1));var target=items[targetIndex];row.scrollTo({left:target.offsetLeft,behavior:"smooth"});};var update=function(){var items=getItems();var total=items.length||1;var active=currentIndex(items);currentEl.textContent=String(active+1);totalEl.textContent=String(total);if(!mobileQuery.matches||items.length<=1){nav.style.display="none";return;}nav.style.display="flex";prev.disabled=active<=0;next.disabled=active>=items.length-1;};prev.addEventListener("click",function(){var items=getItems();scrollToIndex(currentIndex(items)-1);});next.addEventListener("click",function(){var items=getItems();scrollToIndex(currentIndex(items)+1);});row.addEventListener("scroll",function(){if(ticking){return;}ticking=true;window.requestAnimationFrame(function(){update();ticking=false;});},{passive:true});window.addEventListener("resize",update);if(mobileQuery.addEventListener){mobileQuery.addEventListener("change",update);}else if(mobileQuery.addListener){mobileQuery.addListener(update);}update();});};if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init);}else{init();}})();</script>';
        $out .= '</div></div></section>';

        return $out;
    }

    private static function normalizeIds($value): string
    {
        if (is_array($value)) {
            $ids = array_map('absint', $value);
            $ids = array_values(array_filter($ids));
            return implode(',', $ids);
        }

        if (is_string($value)) {
            $parts = array_map('trim', explode(',', $value));
            $ids = array_map('absint', $parts);
            $ids = array_values(array_filter($ids));
            return implode(',', $ids);
        }

        if (is_numeric($value)) {
            $id = absint($value);
            return $id > 0 ? (string) $id : '';
        }

        return '';
    }
}
