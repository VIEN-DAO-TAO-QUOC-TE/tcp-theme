<?php
/**
 * SECTION: SCHOLARSHIP HUNT (Load More Mobile)
 */

// 1. LẤY DATA TỪ ACF hoặc từ args khi gọi get_template_part(..., $args)
$hunt_heading = (isset($args) && is_array($args) && !empty($args['herotitle'])) ? $args['herotitle'] : (get_field('hunt_heading') ?: 'test slide hero');
$hunt_link    = (isset($args) && is_array($args) && !empty($args['hero_link'])) ? $args['hero_link'] : (get_field('hunt_view_more_link') ?: '#');

// Accept hunt list from args (hero cards passed in), otherwise use ACF field
$hunt_scholarship_list = [];
if (isset($args) && is_array($args) && !empty($args['heroCardsTabletDesktop'])) {
    $hunt_scholarship_list = $args['heroCardsTabletDesktop'];
} elseif (isset($args) && is_array($args) && !empty($args['heroCardsMobile'])) {
    $hunt_scholarship_list = $args['heroCardsMobile'];
} else {
    // Removed deprecated ACF `hunt_scholarship_lists` — use site hero cards as fallback
    $hunt_scholarship_list = get_field('tcp_home_hero_cards') ?: get_field('tcp_home_hero_cards_mobile') ?: [];
}

// Duplicate items so Swiper can loop reliably when the source list is short.
$hunt_swiper_items = $hunt_scholarship_list;
if (!empty($hunt_swiper_items) && count($hunt_swiper_items) < 6) {
    while (count($hunt_swiper_items) < 6) {
        $hunt_swiper_items = array_merge($hunt_swiper_items, $hunt_scholarship_list);
    }
}

?>
<div class="bg_hero__homepage">
    <div class="container">
        <div class=" section__scholarship_hunt pt-50 pb-50 section__get__enrollment">
            <div class="section-content relative">

                <div class="row learning-platform__row learning-platform__row--title" id="row-1657928020">
                    <div id="col-1865477843" class="learning-platform__col learning-platform__col--title small-12 large-12">
                        <div class="col-inner">
                            <h1>Nền tảng học tập ứng dụng tích hợp<br><span style="color: #ea580c;"> cố vấn, đào tạo và tư vấn</span></h1>
                        </div>
                    </div>
                </div>


                <div class="row row-full-width">
                    <div class="col small-12 pb-0">
                        <div class="swiper hunt-swiper">
                            <div class="swiper-wrapper">
                                <?php 
                                if ($hunt_swiper_items):
                                    foreach($hunt_swiper_items as $item):
                                        // normalize item to support both ACF hero-card array and WP post object fallback
                                        $p_id = is_object($item) && isset($item->ID) ? $item->ID : 0;

                                        // home_data may exist on post objects
                                        $home_data = $p_id ? get_field('Home_content', $p_id) : [];

                                        // title & desc
                                        if (is_array($item)) {
                                            $title_acf = isset($item['title']) ? $item['title'] : (isset($item['cta_text']) ? $item['cta_text'] : '');
                                            $desc = isset($item['desc']) ? $item['desc'] : '';
                                        } else {
                                            $title_acf = isset($home_data['title']) ? $home_data['title'] : '';
                                            $desc = isset($home_data['desc']) ? $home_data['desc'] : '';
                                        }

                                        $cta_text = '';
                                        $cta_url = '';
                                        // card data may live in Home_content or in the hero card
                                        if (is_array($item)) {
                                            $cta_text = isset($item['cta_text']) ? $item['cta_text'] : '';
                                            $cta_url = isset($item['cta_url']) ? $item['cta_url'] : '';
                                        }
                                        if (empty($cta_text) && isset($home_data['cta_text'])) $cta_text = $home_data['cta_text'];
                                        if (empty($cta_url) && isset($home_data['cta_url'])) $cta_url = $home_data['cta_url'];

                                        $display_title = $title_acf ? $title_acf : ($p_id ? get_the_title($p_id) : '');

                                        // background image: prefer bg_image_url from hero card, fallback to Home_content bg or featured image
                                        $bgImageUrl = '';
                                        if (is_array($item) && !empty($item['bg_image_url'])) {
                                            $bgImageUrl = $item['bg_image_url'];
                                        }
                                        if (empty($bgImageUrl) && isset($home_data['bg_image_url'])) {
                                            $bgField = $home_data['bg_image_url'];
                                            if (is_numeric($bgField)) {
                                                $bgImageUrl = wp_get_attachment_image_url((int)$bgField, 'large');
                                            } else {
                                                $bgImageUrl = (string) $bgField;
                                            }
                                        }

                                        // portrait image (right side): prefer image ID from hero card or Home_content
                                        $imageUrl = '';
                                        if (is_array($item) && !empty($item['image'])) {
                                            $imageId = (int) $item['image'];
                                            $imageUrl = $imageId ? wp_get_attachment_image_url($imageId, 'large') : '';
                                        }
                                        if (empty($imageUrl) && !empty($home_data['image'])) {
                                            $imgField = $home_data['image'];
                                            if (is_numeric($imgField)) {
                                                $imageUrl = wp_get_attachment_image_url((int)$imgField, 'large');
                                            } else {
                                                $imageUrl = (string) $imgField;
                                            }
                                        }

                                        // final fallback: use featured image if available
                                        if (empty($imageUrl) && $p_id) {
                                            $imageUrl = get_the_post_thumbnail_url($p_id, 'large') ?: '';
                                        }

                                    ?>
                                        <div class="swiper-slide">
                                            <div class="hunt-card" style="background-image: url('<?php echo esc_url($bgImageUrl ?: $imageUrl); ?>');">
                                                <div class="hunt-card-left">
                                                    <h3 class="s-name"><?php echo esc_html($display_title); ?></h3>
                                                    <?php if (!empty($desc)): ?>
                                                        <p class="s-desc hide-for-small"><?php echo esc_html($desc); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($cta_text)): ?>
                                                        <a href="<?php echo esc_url($cta_url ?: '#'); ?>" class="hunt-card-cta"><?php echo esc_html($cta_text); ?></a>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($imageUrl)): ?>
                                                    <img src="<?php echo esc_url($imageUrl); ?>" alt="" class="hunt-card-portrait">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php 
                                    endforeach; 
                                endif;
                                ?>
                            </div>
                            <div class="swiper-button-prev hunt-btn-prev" aria-label="Previous"></div>
                            <div class="swiper-button-next hunt-btn-next" aria-label="Next"></div>
                            <div class="swiper-pagination hunt-pagination"></div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>


<style>
/* 1. SECTION STYLE (CHUNG) */
.bg_hero__homepage {background: linear-gradient(180deg, #FFFFFF 0%, #F3F4F6 100%);padding: 60px 0}
.bg_hero__homepage,
.bg_hero__homepage > .container,
.section__scholarship_hunt,
.section__scholarship_hunt .section-content,
.section__scholarship_hunt .row-full-width,
.section__scholarship_hunt .row-full-width > .col {
    overflow: visible;
}
.hunt-title { font-size: 32px; font-weight: 700; color: #263582; margin: 0; }

/* Desktop View More Link */
.hunt-view-more {
    font-size: 16px; font-weight: 600; color: var(--blue-600);
    text-decoration: none; transition: 0.3s;
    display: flex; align-items: center; gap: 10px; justify-content: flex-end;
}
.hunt-view-more i { font-size: 24px!important; }
.hunt-view-more:hover { color: #d93e3e; }

/* 2. DESKTOP CARD STYLE */
.hunt-swiper {
    padding: 10px 0;
    overflow: visible !important;
    width: 100%;
    margin: 0 auto;
}
.hunt-swiper .swiper-slide { height: auto; transition: transform 0.3s; }
.hunt-swiper .swiper-slide-prev {
    pointer-events: none;
}
.hunt-btn-prev,
.hunt-btn-next {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    border-radius: 9999px;
    padding: 7px;
    opacity: 1;
    background: rgba(238, 242, 255, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    top: 50%;
    transform: translateY(-50%);
}
.hunt-btn-prev { left: -16px; }
.hunt-btn-next { right: 12px; }
.hunt-btn-prev::after,
.hunt-btn-next::after {
    font-size: 12px;
    font-weight: 700;
    color: #4f46e5;
}
.hunt-card {
    background: #fffdf3; border-radius: 16px; 
    padding:24px;
    display: flex; justify-content: flex-start; align-items: flex-start;
    height: 100%;
    min-height:280px;
    position: relative;
    background-size: 100%;
    background-position: center center;
    overflow: hidden;
}
.hunt-card-left {
    position: relative;
    z-index: 2;
    flex: 1;
    min-width: 0;
    max-width: 62%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    gap: 12px;
    height: 100%;
    padding-right: 24px;
}
.s-name {
    font-family: var(--font-definitions-font-family-headings);
    font-weight: 700;
    font-style: normal;
    font-size: var(--heading-2-font-size);
    margin: 0;
    line-height: var(--heading-2-line-height);
    letter-spacing: var(--heading-2-letter-spacing);
    color: var(--general-foreground);
    max-width: 560px;
}
.s-desc { display: none; }
.s-stage { display: none; }
.hunt-card-right { display: none; }

.section__scholarship_hunt .row-full-width {
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
}

.section__scholarship_hunt .section-content {
    max-width: 1280px;
    margin: 0 auto;
    padding-left: 20px;
    padding-right: 20px;
}

.section__scholarship_hunt .row-full-width > .col {
    padding-left: 0;
    padding-right: 0;
}

.hunt-pagination {
    position: static;
    margin-top: 18px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 4px;
    padding-left: 0;
    margin-left: 0;
}
.hunt-pagination .swiper-pagination-bullet {
    width: 32px;
    height: 8px;
    border-radius: 999px;
    background: rgba(229, 231, 235, 1);
    opacity: 1;
    transition: width 0.25s ease, background-color 0.25s ease;
}
.hunt-pagination .swiper-pagination-bullet-active {
    width: 8px;
    background: var(--gray-400);
}

.hunt-card-portrait {
    position: absolute;
    right: 0;
    bottom: 0;
    height: 100%;
    width: auto;
    object-fit: cover;
    z-index: 1;
    mask-image: linear-gradient(to left, black 72%, transparent 100%);
}

.hunt-card-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 119px;
    height: 34px;
    min-height: 32px;
    padding: 6px 12px;
    background: #ffffff;
    color: rgba(49, 46, 129, 1);
    border-radius: 8px;
    border-radius: 8px;
    font-family: var(--font-definitions-font-family-body);
    font-size: var(--paragraph-small-font-size);
    font-weight: 500;
    font-style: normal;
    line-height: var(--paragraph-small-line-height);
    letter-spacing: 0.005em;
    text-decoration: none;
    text-align: center;
    vertical-align: middle;
    opacity: 1;
    z-index: 3;
    box-shadow: none;
    flex: 0 0 auto;
}
.hunt-card-cta:hover { transform: translateY(0px); background:#f8f9ff; border-radius: 8px;}

@media screen and (max-width: 849px) {
    .s-name {
        font-size: var(--heading-3-font-size);
        line-height: var(--heading-3-line-height);
        letter-spacing: var(--heading-3-letter-spacing);
        max-width: 194px;
    }
    .hunt-card {
        background-image: url('/wp-content/themes/tcp-theme/assets/images/background/mobile_background.png') !important;
        background-attachment: fixed;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 320px;
    }
    .hunt-card-portrait {
        mask-image:unset;
        max-height: 135px;
        padding-right: 15%;
    }

    .bg_hero__homepage {padding: 30px 0}

    .hunt-card-left {
        position: relative;
        z-index: 2;
        flex: 1;
        min-width: 0;
        display: flex;
        max-width: 100%;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        gap: 12px;
        height: auto;
    }
    
    .hunt-btn-prev,
    .hunt-btn-next {
        display: none;
    }
}


</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Swiper Desktop
    var huntSwiper = new Swiper('.hunt-swiper', {
        slidesPerView: 1.2,
        spaceBetween: 20,
        loop: true,
        loopAdditionalSlides: 2,
        observer: true,
        observeParents: true,
        navigation: {
            nextEl: '.hunt-btn-next',
            prevEl: '.hunt-btn-prev',
        },
        pagination: {
            el: '.hunt-pagination',
            clickable: true,
        },
        breakpoints: {
            850: {
                slidesPerView: 2.2,
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 2.05,
                spaceBetween: 20,
            }
        },
        on: {
            afterInit: function() {
                var self = this;
                // Loop + slidesPerView fractional: lần init đầu Swiper không position
                // prev clone slide đúng. Hack: slideNext + slidePrev (0 duration) sau 1 frame
                // → buộc Swiper re-render với position/class chuẩn cho prev slide.
                requestAnimationFrame(function() {
                    self.slideNext(0);
                    requestAnimationFrame(function() {
                        self.slidePrev(0);
                    });
                });
            }
        }
    });

    // 2. Mobile Load More Logic (jQuery)
    if (typeof jQuery !== 'undefined') {
        jQuery('.mobile-load-more-btn').click(function(e) {
            e.preventDefault();
            jQuery('.mobile-hidden-item').slideDown(200);
            jQuery(this).parent().fadeOut();
        });
    }

});
</script>