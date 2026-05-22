<?php
/**
 * Search results template.
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

get_header();

$searchQuery = get_search_query();
$rawType = isset($_GET['type']) ? sanitize_key((string) wp_unslash($_GET['type'])) : 'course';
$activeType = in_array($rawType, ['course', 'blog'], true) ? $rawType : 'course';

$paged = max(1, (int) get_query_var('paged'));
if ($paged === 1) {
    $paged = max(1, (int) get_query_var('page'));
}

$perPage = 8;

$tabs = [
    [
        'slug'  => 'course',
        'label' => __('Khóa học', 'tcp-theme'),
    ],
    [
        'slug'  => 'blog',
        'label' => __('C-Sherpa Blog', 'tcp-theme'),
    ],
];

$tabBaseArgs = ['s' => $searchQuery];

$resultsQuery = null;
if ($searchQuery !== '') {
    $queryArgs = [
        's'              => $searchQuery,
        'post_status'    => 'publish',
        'posts_per_page' => $perPage,
        'paged'          => $paged,
    ];

    if ($activeType === 'course') {
        $queryArgs['post_type'] = 'product';
        $queryArgs['fields']    = 'ids';
    } else {
        $queryArgs['post_type'] = 'post';
        $queryArgs['ignore_sticky_posts'] = true;
    }

    $resultsQuery = new WP_Query($queryArgs);
}

$totalPages = $resultsQuery instanceof WP_Query ? (int) $resultsQuery->max_num_pages : 0;
?>

<main class="tcp-search-page" id="main" role="main">
    <section class="tcp-search-page__hero" aria-label="<?php echo esc_attr__('Thanh tìm kiếm', 'tcp-theme'); ?>">
        <div class="tcp-search-page__hero-inner">
            <?php get_search_form(); ?>
        </div>
    </section>

    <section class="tcp-search-page__results">
        <div class="tcp-search-page__shell">
            <header class="tcp-search-page__header">
                <h1 class="tcp-search-page__title"><?php echo esc_html__('Kết quả tìm kiếm', 'tcp-theme'); ?></h1>
                <?php if ($searchQuery !== '') : ?>
                    <p class="tcp-search-page__subtitle">
                        <?php
                        echo esc_html(sprintf(
                            /* translators: %s: search keyword */
                            __('Cho từ khóa "%s"', 'tcp-theme'),
                            $searchQuery
                        ));
                        ?>
                    </p>
                <?php endif; ?>
            </header>

            <nav class="tcp-search-page__tabs" aria-label="<?php echo esc_attr__('Lọc kết quả theo loại', 'tcp-theme'); ?>">
                <?php foreach ($tabs as $tab) :
                    $tabArgs = $tabBaseArgs;
                    $tabArgs['type'] = $tab['slug'];
                    $tabHref = add_query_arg($tabArgs, home_url('/'));
                    $isActive = $activeType === $tab['slug'];
                ?>
                    <a
                        class="tcp-search-page__tab<?php echo $isActive ? ' is-active' : ''; ?>"
                        href="<?php echo esc_url($tabHref); ?>"
                        <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                    ><?php echo esc_html($tab['label']); ?></a>
                <?php endforeach; ?>
            </nav>

            <?php if ($searchQuery === '') : ?>
                <div class="tcp-search-page__empty">
                    <p><?php echo esc_html__('Nhập từ khóa vào ô tìm kiếm phía trên để bắt đầu.', 'tcp-theme'); ?></p>
                </div>
            <?php elseif (!$resultsQuery instanceof WP_Query || !$resultsQuery->have_posts()) : ?>
                <div class="tcp-search-page__empty">
                    <p>
                        <?php
                        echo esc_html(sprintf(
                            /* translators: %s: search keyword */
                            __('Không tìm thấy kết quả phù hợp với "%s".', 'tcp-theme'),
                            $searchQuery
                        ));
                        ?>
                    </p>
                </div>
            <?php elseif ($activeType === 'course') : ?>
                <?php
                $ids = array_map('intval', (array) $resultsQuery->posts);
                $ids = array_values(array_filter($ids, static fn($id) => $id > 0));
                $idsStr = implode(',', $ids);
                ?>
                <div class="tcp-search-page__grid tcp-search-page__grid--course">
                    <?php echo do_shortcode('[ux_products type="row" columns="4" columns__md="2" columns__sm="1" infinitive="false" equalize_box="true" ids="' . esc_attr($idsStr) . '"]'); ?>
                </div>
            <?php else : ?>
                <div class="tcp-search-page__grid tcp-search-page__grid--blog">
                    <?php while ($resultsQuery->have_posts()) : $resultsQuery->the_post();
                        $postId = get_the_ID();
                        $postTitle = get_the_title($postId);
                        $postUrl = get_permalink($postId);
                        $postDate = get_the_date('F j, Y', $postId);
                        $postExcerpt = get_the_excerpt($postId);
                        if (trim((string) $postExcerpt) === '') {
                            $postExcerpt = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $postId)), 22, '...');
                        }
                        $contentWords = str_word_count(wp_strip_all_tags((string) get_post_field('post_content', $postId)));
                        $readingMinutes = max(1, (int) ceil($contentWords / 200));
                    ?>
                        <article class="category-post-card">
                            <a class="category-post-card__thumb" href="<?php echo esc_url($postUrl); ?>" aria-label="<?php echo esc_attr($postTitle); ?>">
                                <?php if (has_post_thumbnail($postId)) : ?>
                                    <?php echo get_the_post_thumbnail($postId, 'medium_large'); ?>
                                <?php else : ?>
                                    <img src="<?php echo esc_url(TCP_THEME_URI . 'screenshot.png'); ?>" alt="<?php echo esc_attr($postTitle); ?>" loading="lazy" />
                                <?php endif; ?>
                            </a>

                            <div class="category-post-card__content">
                                <h3 class="category-post-card__title">
                                    <a href="<?php echo esc_url($postUrl); ?>"><?php echo esc_html($postTitle); ?></a>
                                </h3>

                                <?php if (is_string($postExcerpt) && trim($postExcerpt) !== '') : ?>
                                    <p class="category-post-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($postExcerpt), 22, '...')); ?></p>
                                <?php endif; ?>

                                <p class="category-post-card__meta">
                                    <?php echo esc_html(sprintf(_n('%s phút đọc', '%s phút đọc', $readingMinutes, 'tcp-theme'), number_format_i18n($readingMinutes))); ?>
                                    <span class="category-post-card__meta-sep" aria-hidden="true">&middot;</span>
                                    <time datetime="<?php echo esc_attr(get_the_date('c', $postId)); ?>"><?php echo esc_html($postDate); ?></time>
                                </p>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php endif; ?>

            <?php
            if ($resultsQuery instanceof WP_Query && $totalPages > 1) :
                $bigInt = 999999999;
                $paginationLinks = paginate_links([
                    'base'      => str_replace((string) $bigInt, '%#%', esc_url(get_pagenum_link($bigInt))),
                    'format'    => '?paged=%#%',
                    'add_args'  => array_filter([
                        's'    => $searchQuery,
                        'type' => $activeType,
                    ]),
                    'current'   => $paged,
                    'total'     => $totalPages,
                    'prev_text' => __('Previous', 'tcp-theme'),
                    'next_text' => __('Next', 'tcp-theme'),
                    'type'      => 'array',
                ]);

                if (!empty($paginationLinks)) :
            ?>
                <nav class="tcp-search-page__pagination" aria-label="<?php echo esc_attr__('Phân trang kết quả', 'tcp-theme'); ?>">
                    <?php foreach ($paginationLinks as $link) :
                        $isPrev = strpos($link, 'prev page-numbers') !== false;
                        $isNext = strpos($link, 'next page-numbers') !== false;
                        $isCurrent = strpos($link, 'current') !== false;
                        $isDots = strpos($link, 'dots') !== false;
                        $classes = ['tcp-search-page__page'];
                        if ($isPrev) {
                            $classes[] = 'tcp-search-page__page--prev';
                        } elseif ($isNext) {
                            $classes[] = 'tcp-search-page__page--next';
                        } elseif ($isCurrent) {
                            $classes[] = 'tcp-search-page__page--current';
                        } elseif ($isDots) {
                            $classes[] = 'tcp-search-page__page--dots';
                        } else {
                            $classes[] = 'tcp-search-page__page--number';
                        }
                    ?>
                        <span class="<?php echo esc_attr(implode(' ', $classes)); ?>"><?php echo wp_kses_post($link); ?></span>
                    <?php endforeach; ?>
                </nav>
            <?php endif; endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
