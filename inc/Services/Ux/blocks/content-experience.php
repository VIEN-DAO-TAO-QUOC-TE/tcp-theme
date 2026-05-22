<?php
/**
 * SECTION: STUDENT EXPERIENCE
 * Features: 
 * - Desktop: Nav controls top-right (Fraction).
 * - Mobile: Line pagination bottom (Bullets).
 * - Data: Dynamic from ACF/Posts.
 */

// --- 1. LẤY DỮ LIỆU ---
$exp_main_heading = get_field('exp_main_heading') ?: 'Trải nghiệm của sinh viên';
$anchor_input     = get_field('experience_anchor') ?? '#experience';
$anchor_id        = sanitize_title(str_replace('#', '', $anchor_input));

// Lấy danh sách bài viết (Video/Review)
$selected_students = get_field('experience_selected_videos'); 
if ( empty($selected_students) ) {
    $selected_students = get_posts([
        'post_type'      => 'videos', // Thay bằng post type thực tế của bạn
        'posts_per_page' => 10, // Tăng lên 10 để test scroll (nếu cần)
        'post_status'    => 'publish',
    ]);
}

// Bắt đầu bộ nhớ đệm (Output Buffering) để gom HTML
ob_start();
?>

<div id="<?php echo esc_attr($anchor_id); ?>" style="position: relative; top: -50px; visibility: hidden;"></div>

<div class="section section__student_exp">
    <div class="section-content relative">
        
        <div class="row align-middle mb-30 header-row">
            
            <div class="col medium-8 small-12">
                <h2 class="section-title text-white mb-0"><?php echo esc_html($exp_main_heading); ?></h2>
            </div>

            <div class="col medium-4 small-12 hide-for-medium">
                <div class="exp-nav-wrapper">
                    <div class="exp-nav-controls">
                        <div class="swiper-button-prev exp-btn-prev"></div>
                        
                        <div class="exp-pagination-desktop"></div>
                        
                        <div class="swiper-button-next exp-btn-next"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row row-full-width">
            <div class="col small-12">
                
                <div class="swiper exp-swiper">
                    <div class="swiper-wrapper">
                        
                        <?php if( !empty($selected_students) ): 
                            foreach( $selected_students as $post_obj ):
                                $p_id = $post_obj->ID;
                                
                                // Lấy thông tin bài viết
                                $s_name = get_field('talent_name', $p_id);
                                $s_desc = get_the_excerpt($p_id); // Hoặc get_field('review_quote', $p_id)
                                // Lấy chức danh (ACF field: student_role)
                                $s_role = get_field('student_role', $p_id) ?: 'Sinh viên Global Pathways';
                                
                                // Lấy ảnh đại diện
                                $s_img_url = get_the_post_thumbnail_url($p_id, 'large');
                                if(!$s_img_url) $s_img_url = 'https://via.placeholder.com/300x400.png?text=Student';

                                // Link Video Popup (ACF field: link_video)
                                $video_link = get_field('link_video', $p_id) ?: '#'; 
                        ?>
                            <div class="swiper-slide">
                                <div class="student-review-card">
                                    <div class="card-content">
                                        <p class="review-quote">“<?php echo wp_trim_words($s_desc, 15, '...'); ?>”</p>
                                        
                                        <div class="student-info">
                                            <h3 class="student-name"><?php echo esc_html($s_name); ?></h3>
                                            <p class="student-role"><?php echo esc_html($s_role); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="card-image-wrapper">
                                        <img src="<?php echo esc_url($s_img_url); ?>" class="student-img" alt="<?php echo esc_attr($s_name); ?>">
                                    </div>

                                    <?php 
                                    if( !empty($video_link) && $video_link !== '#' ): 
                                        echo do_shortcode('[button text="" link="'.$video_link.'" class="is-lightbox video-play-btn" icon="icon-play" style="outline" radius="99"]');
                                    endif; 
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>

                    </div>
                    
                    <div class="swiper-pagination hide-for-large exp-pagination-mobile show-for-small-only"></div>

                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* --- 1. SECTION STYLE --- */
.section__student_exp {
    background-color: var(--blue-600, #3660F2); /* Fallback màu xanh nếu biến chưa define */
    overflow: hidden;
    padding: 40px 0;
}
.text-white { color: #ffffff !important; }

/* --- 2. DESKTOP NAV (Góc Phải) --- */
.exp-nav-wrapper { display: flex; justify-content: flex-end; }
.exp-nav-controls { display: flex; align-items: center; gap: 15px; }

/* Button Prev/Next */
.exp-btn-prev, .exp-btn-next {
    position: static !important; margin: 0 !important;
    width: 40px; height: 40px;
    background-color: #ffffff; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: 0.3s;
}
.exp-btn-prev::after, .exp-btn-next::after {
    font-size: 16px; font-weight: bold; color: #3660F2;
}
.exp-btn-prev:hover, .exp-btn-next:hover { background-color: #1a237e; }
.exp-btn-prev:hover::after, .exp-btn-next:hover::after { color: #ffffff; }

/* Desktop Counter (1 / 5) */
.exp-pagination-desktop {
    color: #ffffff; font-weight: 600; font-size: 16px;
    letter-spacing: 1px; min-width: 50px; text-align: center;
}

/* --- 3. MOBILE PAGINATION (LINE STYLE) --- */
.exp-swiper .exp-pagination-mobile {
    position: static; /* Nằm dưới slide */
    margin-top: 25px;
    display: flex; justify-content: center; align-items: center;
    gap: 6px; /* Khoảng cách giữa các line */
}

/* Style từng Line */
.exp-pagination-mobile .swiper-pagination-bullet {
    width: 20px;          /* Chiều rộng */
    height: 4px;          /* Chiều cao */
    background: #ffffff;  /* Màu trắng */
    border-radius: 4px;   /* Bo góc nhẹ */
    opacity: 0.3;         /* Mờ khi không active */
    margin: 0 !important;
    transition: all 0.3s ease;
}

/* Style Line Active */
.exp-pagination-mobile .swiper-pagination-bullet-active {
    opacity: 1;           /* Sáng rõ */
    width: 20px;          /* Giữ nguyên width hoặc tăng lên nếu muốn */
    background: #ffffff;
}

/* --- 4. CARD STYLE --- */
.exp-swiper { padding: 10px; overflow: visible !important; }
.exp-swiper .swiper-slide { width: 350px; height: auto; transition: transform 0.3s; }

.student-review-card {
    background-color: #1A237E; border-radius: 20px; padding: 24px;
    position: relative; height: 220px; display: flex; overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.card-content { width: 65%; color: #fff; z-index: 2; display: flex; flex-direction: column; justify-content: space-between; }
.review-quote { font-size: 16px; line-height: 1.4; font-style: italic; margin-bottom: 10px; }
.student-name { font-size: 16px; font-weight: 700; margin: 0; color: #fff; }
.student-role { font-size: 12px; opacity: 0.8; margin: 0; }
.card-image-wrapper { position: absolute; right: -20%; bottom: 0; height: 100%; width: 90%; }
.student-img { position: absolute; right: 0; bottom: 0; height: 90%; width: auto; object-fit: cover; mask-image: linear-gradient(to top, black 80%, transparent 100%); }
.play-btn { position: absolute; bottom: 20px; right: 20px; width: 35px; height: 35px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10; }
.play-icon { width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 8px solid #1A237E; margin-left: 2px; }
.video-play-btn {
    position: absolute !important;
    bottom: 20px;
    right: 20px;
    z-index: 10;
    
    /* Biến nút thành hình tròn trắng */
    background-color: #ffffff !important;
    border: none !important;
    color: #1A237E !important; /* Màu icon */
    border-radius: 50% !important;
    width: 40px !important;
    height: 40px !important;
    min-width: 0 !important;
    padding: 0 !important;
    
    /* Căn giữa icon */
    display: flex !important;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
/* Icon bên trong nút */
.video-play-btn i {
    font-size: 18px !important;
    margin: 0 !important;
    margin-left: 0 !important; /* Căn chỉnh icon play cho cân đối */
}
.video-play-btn:hover {
    background-color: #f0f0f0 !important;
    transform: scale(1.1);
}
/* Responsive Mobile */
@media (max-width: 768px) {
    .exp-swiper {padding:0}
    .section__student_exp .col { padding-bottom: 0 !important; }

    .header-row { flex-direction: column; align-items: flex-start; margin-bottom: 20px; }
    .exp-swiper .swiper-slide { width: auto; } 
    .card-content {width: 50%;}
    /* Desktop controls are hidden via 'hide-for-small' class */
}

@media (max-width: 549px) {
    .exp-swiper .swiper-slide { width: 100%; } 

}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cấu hình số lượng item mỗi nhóm
    const GROUP_SIZE = 5; 

    var expSwiper = new Swiper('.exp-swiper', {
        slidesPerView: 'auto', 
        spaceBetween: 35,    
        
        // Tắt Loop để tính toán chính xác
        loop: false,         
        
        navigation: {
            nextEl: '.exp-btn-next',
            prevEl: '.exp-btn-prev',
            disabledClass: 'swiper-button-disabled',
        },
        
        pagination: {
            el: '.exp-pagination-mobile',
            clickable: true,
            type: 'bullets', 
        },
        
        // Gọi hàm update khi init và khi slide thay đổi
        on: {
            init: updateDesktopCounter,
            slideChange: updateDesktopCounter
        },

        breakpoints: {
            // Mobile: Scroll 1 cái
            0: {
                slidesPerGroup: 1,
            },
            // Desktop: Scroll 5 cái
            768: { 
                spaceBetween: 30,
                slidesPerGroup: GROUP_SIZE // = 5
            }
        }
    });

    // --- HÀM TÍNH TOÁN THỦ CÔNG ---
    function updateDesktopCounter() {
        // 1. Lấy tổng số bài viết thật
        // (Trừ đi các slide duplicate nếu lỡ bật loop, dù ở đây đã tắt)
        var totalSlides = this.slides.length;
        if(this.params.loop) {
            totalSlides -= (this.loopedSlides * 2);
        }
        
        // 2. Tính Tổng số trang (Làm tròn lên)
        // Ví dụ: 10 bài / 5 = 2 trang. 12 bài / 5 = 3 trang.
        var totalPages = Math.ceil(totalSlides / GROUP_SIZE);
        
        // 3. Tính Trang hiện tại
        // realIndex là chỉ số slide hiện tại (0, 1, 2... 5... 10...)
        var currentSlideIndex = this.realIndex; 
        var currentPage = Math.floor(currentSlideIndex / GROUP_SIZE) + 1;

        // Xử lý logic biên (đảm bảo không vượt quá tổng)
        if (currentPage > totalPages) currentPage = totalPages;

        // 4. Render ra HTML
        var counterEl = document.querySelector('.exp-pagination-desktop');
        if(counterEl) {
            counterEl.innerHTML = `<span>${currentPage}</span> / <span>${totalPages}</span>`;
        }
    }
});
</script>

<?php
// Output nội dung
$content_html = ob_get_clean();
echo do_shortcode($content_html);
?>