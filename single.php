<?php
/**
 * Default single post template.
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
    the_post();

    $postId = get_the_ID();
    if ($postId <= 0) {
        $postId = absint((string) get_queried_object_id());
    }

    $postTitle = get_the_title($postId);
    $postUrl = get_permalink($postId);
    $postDate = get_the_date('d/m/Y', $postId);
    $postAuthorId = (int) get_post_field('post_author', $postId);
    $postAuthor = $postAuthorId > 0 ? get_the_author_meta('display_name', $postAuthorId) : '';

    $blogPageId = (int) get_option('page_for_posts');
    $blogPageLink = $blogPageId > 0 ? get_permalink($blogPageId) : home_url('/');

    $categories = get_the_category($postId);
    $primaryCategory = ($categories && !is_wp_error($categories) && isset($categories[0]) && $categories[0] instanceof WP_Term) ? $categories[0] : null;
    $primaryCategoryLink = '';
    if ($primaryCategory instanceof WP_Term) {
        $categoryLink = get_term_link($primaryCategory);
        if (!is_wp_error($categoryLink)) {
            $primaryCategoryLink = (string) $categoryLink;
        }
    }

    $tags = get_the_tags($postId);
    $authorAvatar = $postAuthorId > 0 ? get_avatar($postAuthorId, 38, '', $postAuthor !== '' ? $postAuthor : $postTitle, ['class' => 'tcp-single-post__author-avatar']) : '';

    $rawContent = trim((string) get_post_field('post_content', $postId));
    $contentHtml = $rawContent !== '' ? apply_filters('the_content', $rawContent) : '';
    if (trim((string) wp_strip_all_tags($contentHtml)) === '') {
        $excerpt = get_the_excerpt($postId);
        if (is_string($excerpt) && trim($excerpt) !== '') {
            $contentHtml = wpautop(esc_html(trim($excerpt)));
        }
    }

    $summarySource = trim((string) wp_strip_all_tags($contentHtml));
    if ($summarySource === '') {
        $summarySource = trim((string) get_the_excerpt($postId));
    }
    $readingTime = max(1, (int) ceil(str_word_count($summarySource) / 220));

    $shareUrl = rawurlencode($postUrl ?: home_url('/'));
    $shareTitle = rawurlencode($postTitle);
    $shareIconSources = [
        [
            'path' => trailingslashit(get_stylesheet_directory()) . 'assets/images/share/',
            'url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/share/',
        ],
    ];
    if (get_template_directory() !== get_stylesheet_directory()) {
        $shareIconSources[] = [
            'path' => trailingslashit(get_template_directory()) . 'assets/images/share/',
            'url' => trailingslashit(get_template_directory_uri()) . 'assets/images/share/',
        ];
    }
    $shareLinks = [
        [
            'label' => 'Facebook',
            'href' => 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl,
            'icon' => 'facebook.png',
            'svg' => '<path d="M13.5 3h2.5V0h-2.5C10.85 0 9 1.79 9 5v2H6v3h3v10h4V10h3.2l.5-3H13V5c0-.88.22-2 1.5-2Z"/>',
        ],
        [
            'label' => 'Instagram',
            'href' => 'https://www.instagram.com/',
            'icon' => 'instagram.png',
            'svg' => '<path d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9a5.5 5.5 0 0 1-5.5 5.5h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2Zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9Zm9.75 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>',
        ],
        [
            'label' => 'X',
            'href' => 'https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle,
            'icon' => 'x.png',
            'svg' => '<path d="M3 3h4.7l4.1 5.8L16.8 3H21l-7 9.4L21 21h-4.7l-4.4-6.2L7.2 21H3l7.4-9.9L3 3Zm3.2 1.8h1.7l10.1 14.3h-1.7L6.2 4.8Z"/>',
        ],
        [
            'label' => 'LinkedIn',
            'href' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl,
            'icon' => 'in.png',
            'svg' => '<path d="M4.5 8.2H.9V21h3.6V8.2Zm-1.8-1.8A2.1 2.1 0 1 0 2.7 2a2.1 2.1 0 0 0 0 4.4ZM21 21h-3.6v-6.6c0-1.6 0-3.7-2.3-3.7-2.4 0-2.7 1.8-2.7 3.6V21H8.8V8.2h3.5v1.7h.1c.5-.9 1.8-1.9 3.7-1.9 4 0 4.8 2.6 4.8 6V21Z"/>',
        ],
    ];

    $relatedQueryArgs = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => [$postId],
        'ignore_sticky_posts' => true,
    ];

    if ($primaryCategory instanceof WP_Term) {
        $relatedQueryArgs['cat'] = (int) $primaryCategory->term_id;
    }

    $relatedQuery = new WP_Query($relatedQueryArgs);
    ?>

    <main class="tcp-single-post" id="main" role="main">
        <div class="tcp-single-post__shell">
            <nav class="tcp-single-post__breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Trang chủ', 'tcp-theme'); ?></a>
                <span class="tcp-single-post__breadcrumb-sep">&gt;</span>
                <a href="<?php echo esc_url($blogPageLink); ?>"><?php echo esc_html__('Bài viết', 'tcp-theme'); ?></a>
                <?php if ($primaryCategory instanceof WP_Term && $primaryCategoryLink !== '') : ?>
                    <span class="tcp-single-post__breadcrumb-sep">&gt;</span>
                    <a href="<?php echo esc_url($primaryCategoryLink); ?>"><?php echo esc_html($primaryCategory->name); ?></a>
                <?php endif; ?>
                <span class="tcp-single-post__breadcrumb-sep">&gt;</span>
                <span class="tcp-single-post__breadcrumb-current"><?php echo esc_html($postTitle); ?></span>
            </nav>

            <article <?php post_class('tcp-single-post__article'); ?> id="post-<?php echo esc_attr((string) $postId); ?>">
                <header class="tcp-single-post__header">
                    <div class="tcp-single-post__header-main">
                        <?php if ($primaryCategory instanceof WP_Term) : ?>
                            <div class="tcp-single-post__eyebrow"><?php echo esc_html($primaryCategory->name); ?></div>
                        <?php endif; ?>

                        <h1 class="tcp-single-post__title"><?php echo esc_html($postTitle); ?></h1>

                        <?php if (has_excerpt($postId)) : ?>
                            <div class="tcp-single-post__excerpt"><?php echo wp_kses_post(wpautop(get_the_excerpt($postId))); ?></div>
                        <?php endif; ?>

                        <div class="tcp-single-post__author-row">
                            <?php if ($authorAvatar !== '') : ?>
                                <?php echo $authorAvatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endif; ?>
                            <div class="tcp-single-post__author-copy">
                                <div class="tcp-single-post__author-name"><?php echo esc_html($postAuthor !== '' ? $postAuthor : get_bloginfo('name')); ?></div>
                                <div class="tcp-single-post__author-role"><?php echo esc_html__('Author', 'tcp-theme'); ?></div>
                            </div>
                        </div>
                    </div>

                    <aside class="tcp-single-post__header-aside">
                        <div class="tcp-single-post__meta-block">
                            <p class="tcp-single-post__meta-label"><?php echo esc_html__('NGÀY ĐĂNG', 'tcp-theme'); ?></p>
                            <p class="tcp-single-post__meta-value"><?php echo esc_html($postDate); ?></p>
                        </div>

                        <div class="tcp-single-post__meta-block">
                            <p class="tcp-single-post__meta-label"><?php echo esc_html__('PHÂN LOẠI', 'tcp-theme'); ?></p>
                            <div class="tcp-single-post__chips">
                                <?php
                                $chipTerms = [];
                                if ($primaryCategory instanceof WP_Term) {
                                    $chipTerms[] = $primaryCategory->name;
                                }
                                if (!empty($tags) && !is_wp_error($tags)) {
                                    foreach ($tags as $tag) {
                                        if ($tag instanceof WP_Term) {
                                            $chipTerms[] = $tag->name;
                                        }
                                    }
                                }
                                $chipTerms = array_values(array_unique(array_filter(array_map(static fn($value): string => is_string($value) ? trim($value) : '', $chipTerms))));
                                if (empty($chipTerms)) {
                                    $chipTerms = ['Blog'];
                                }
                                foreach ($chipTerms as $chipTerm) :
                                ?>
                                    <span class="tcp-single-post__chip"><?php echo esc_html($chipTerm); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </aside>
                </header>

                <div class="tcp-single-post__body">
                    <aside class="tcp-single-post__share-rail" aria-label="Share article">
                        <span class="tcp-single-post__share-label">SHARE</span>
                        <div class="tcp-single-post__share-list">
                            <?php foreach ($shareLinks as $shareLink) : ?>
                                <?php
                                $shareIconUrl = '';
                                if (isset($shareLink['icon']) && is_string($shareLink['icon']) && $shareLink['icon'] !== '') {
                                    $iconFileName = sanitize_file_name($shareLink['icon']);
                                    foreach ($shareIconSources as $shareIconSource) {
                                        $iconFilePath = $shareIconSource['path'] . $iconFileName;
                                        if (is_file($iconFilePath)) {
                                            $shareIconUrl = $shareIconSource['url'] . $iconFileName;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <a class="tcp-single-post__share-btn" href="<?php echo esc_url($shareLink['href']); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($shareLink['label']); ?>">
                                    <?php if ($shareIconUrl !== '') : ?>
                                        <img class="tcp-single-post__share-icon-image" src="<?php echo esc_url($shareIconUrl); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async" />
                                    <?php else : ?>
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <?php echo $shareLink['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </svg>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </aside>

                    <div class="tcp-single-post__content-wrap">
                        <div class="tcp-single-post__content entry-content">
                            <?php if ($contentHtml !== '') : ?>
                                <?php echo $contentHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php else : ?>
                                <p><?php echo esc_html__('Nội dung bài viết đang được cập nhật.', 'tcp-theme'); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($tags) && !is_wp_error($tags)) : ?>
                            <div class="tcp-single-post__tags" aria-label="Post tags">
                                <?php foreach ($tags as $tag) : ?>
                                    <?php if ($tag instanceof WP_Term) : ?>
                                        <?php $tagLink = get_term_link($tag); ?>
                                        <?php if (!is_wp_error($tagLink)) : ?>
                                            <a class="tcp-single-post__tag" href="<?php echo esc_url((string) $tagLink); ?>"><?php echo esc_html($tag->name); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </article>

            <?php if ($relatedQuery->have_posts()) : ?>
                <section class="tcp-single-post__related" aria-label="Related posts">
                    <div class="tcp-single-post__section-head">
                        <h2 class="tcp-single-post__section-title"><?php echo esc_html__('Bài viết liên quan', 'tcp-theme'); ?></h2>
                        <a class="tcp-single-post__section-link" href="<?php echo esc_url($blogPageLink); ?>"><?php echo esc_html__('Xem tất cả', 'tcp-theme'); ?></a>
                    </div>

                    <div class="tcp-single-post__related-swiper swiper" aria-label="Related posts slider">
                        <div class="tcp-single-post__related-grid swiper-wrapper">
                            <?php while ($relatedQuery->have_posts()) : $relatedQuery->the_post(); ?>
                            <?php
                            $relatedId = get_the_ID();
                            $relatedTitle = get_the_title($relatedId);
                            $relatedUrl = get_permalink($relatedId);
                            $relatedDate = get_the_date('F j, Y', $relatedId);
                            $relatedExcerpt = get_the_excerpt($relatedId);
                            if (trim((string) $relatedExcerpt) === '') {
                                $relatedExcerpt = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $relatedId)), 22, '...');
                            }
                            $relatedContentWords = str_word_count(wp_strip_all_tags(get_post_field('post_content', $relatedId)));
                            $relatedReadingMinutes = max(1, (int) ceil($relatedContentWords / 200));
                            ?>
                            <article class="category-post-card swiper-slide">
                                <a class="category-post-card__thumb" href="<?php echo esc_url($relatedUrl); ?>" aria-label="<?php echo esc_attr($relatedTitle); ?>">
                                    <?php if (has_post_thumbnail($relatedId)) : ?>
                                        <?php echo get_the_post_thumbnail($relatedId, 'medium_large'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(TCP_THEME_URI . 'screenshot.png'); ?>" alt="<?php echo esc_attr($relatedTitle); ?>" loading="lazy" />
                                    <?php endif; ?>
                                    <span class="category-post-card__badge"><?php echo esc_html__('ĐÁNG CHÚ Ý', 'tcp-theme'); ?></span>
                                </a>

                                <div class="category-post-card__content">
                                    <h3 class="category-post-card__title">
                                        <a href="<?php echo esc_url($relatedUrl); ?>"><?php echo esc_html($relatedTitle); ?></a>
                                    </h3>

                                    <?php if (is_string($relatedExcerpt) && trim($relatedExcerpt) !== '') : ?>
                                        <p class="category-post-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($relatedExcerpt), 22, '...')); ?></p>
                                    <?php endif; ?>

                                    <p class="category-post-card__meta">
                                        <?php echo esc_html(sprintf(_n('%s phút đọc', '%s phút đọc', $relatedReadingMinutes, 'tcp-theme'), number_format_i18n($relatedReadingMinutes))); ?>
                                        <span class="category-post-card__meta-sep" aria-hidden="true">&middot;</span>
                                        <time datetime="<?php echo esc_attr(get_the_date('c', $relatedId)); ?>"><?php echo esc_html($relatedDate); ?></time>
                                    </p>
                                </div>
                            </article>
                                                        <?php endwhile; ?>
                                                </div>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

        <script>
            (function () {
                var slider = document.querySelector('.tcp-single-post__related-swiper');
                if (!slider) {
                    return;
                }

                var swiperInstance = null;
                var mobileMaxWidth = 849;

                var setupSwiper = function () {
                    var isMobile = window.matchMedia('(max-width: ' + mobileMaxWidth + 'px)').matches;

                    if (!isMobile) {
                        if (swiperInstance) {
                            swiperInstance.destroy(true, true);
                            swiperInstance = null;
                        }
                        slider.classList.remove('is-swiper-active');
                        return;
                    }

                    slider.classList.add('is-swiper-active');
                    if (swiperInstance || typeof window.Swiper === 'undefined') {
                        return;
                    }

                    swiperInstance = new window.Swiper(slider, {
                        slidesPerView: 'auto',
                        spaceBetween: 16,
                        speed: 450,
                        watchOverflow: true,
                    });
                };

                var ensureSwiperAssets = function (cb) {
                    if (typeof window.Swiper !== 'undefined') {
                        cb();
                        return;
                    }

                    if (!document.querySelector('link[data-tcp-swiper-css]')) {
                        var cssLink = document.createElement('link');
                        cssLink.rel = 'stylesheet';
                        cssLink.href = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css';
                        cssLink.setAttribute('data-tcp-swiper-css', '1');
                        document.head.appendChild(cssLink);
                    }

                    var existingScript = document.querySelector('script[data-tcp-swiper-js]');
                    if (existingScript) {
                        existingScript.addEventListener('load', cb, { once: true });
                        return;
                    }

                    var script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';
                    script.async = true;
                    script.setAttribute('data-tcp-swiper-js', '1');
                    script.addEventListener('load', cb, { once: true });
                    document.head.appendChild(script);
                };

                ensureSwiperAssets(function () {
                    setupSwiper();
                    window.addEventListener('resize', setupSwiper);
                });
            })();
        </script>

    <?php
    wp_reset_postdata();
endwhile;

get_footer();