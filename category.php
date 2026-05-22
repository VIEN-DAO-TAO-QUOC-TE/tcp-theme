<?php
/**
 * Category Template
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

use TCP\Theme\Services\Ux\Sections\About\AboutBreadcrumbSection;

get_header();

$category = get_queried_object();
$subtree_slug = 'khong-gian-tri-thuc'; // Root category slug
$root_category = ($category instanceof WP_Term && $category->taxonomy === 'category') ? get_term_by('slug', $subtree_slug, 'category') : null;
$root_id = ($root_category instanceof WP_Term) ? (int) $root_category->term_id : 0;
$is_in_subtree = false;

if ($category instanceof WP_Term && $category->taxonomy === 'category' && $root_id > 0) {
    $is_in_subtree = ((int) $category->term_id === $root_id) || term_is_ancestor_of($root_id, (int) $category->term_id, 'category');
}

$archive_eyebrow = '';
if ($category instanceof WP_Term && $category->taxonomy === 'category') {
    $archive_eyebrow = ($is_in_subtree && $root_category instanceof WP_Term)
        ? (string) $root_category->name
        : (string) $category->name;
}

$archive_title = ($category instanceof WP_Term && $category->taxonomy === 'category')
    ? single_term_title('', false)
    : '';
$archive_description = category_description();

echo '<div class="category-archive__breadcrumb">';
echo AboutBreadcrumbSection::render();
echo '</div>';

$show_child_grid = false;
$rendered_child_grid = '';
if ($category instanceof WP_Term && $category->taxonomy === 'category') {
    if ($is_in_subtree) {
        $child_categories = get_terms([
            'taxonomy' => 'category',
            'parent' => (int) $category->term_id,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        $show_child_grid = is_array($child_categories) && !empty($child_categories);

        if ($show_child_grid) {
            $rendered_child_grid = \TCP\Theme\Services\Ux\Sections\CategorySection::render([
                'parentCategoryId' => (int) $category->term_id,
            ]);

            // If category grid cannot render for any reason, fall back to post listing.
            if (trim($rendered_child_grid) === '') {
                $show_child_grid = false;
            }
        }
    }
}

if ($show_child_grid) {
    ?>
    <div class="container">
        <div class="category-posts category-posts--child-grid">
            <header class="archive-header">
                <?php if ($archive_eyebrow !== '') : ?>
                    <p class="archive-eyebrow"><?php echo esc_html(mb_strtoupper($archive_eyebrow)); ?></p>
                <?php endif; ?>

                <?php if ($archive_title !== '') : ?>
                    <h1 class="page-title"><?php echo esc_html($archive_title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($archive_description)) : ?>
                    <div class="category-description">
                        <?php echo wp_kses_post($archive_description); ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php echo $rendered_child_grid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    </div>
    <?php
} else {
    // Leaf category (or outside subtree): show posts
    global $wp_query;

    $posts_query = $wp_query;
    $used_fallback_query = false;

    if ((!$posts_query instanceof WP_Query || !$posts_query->have_posts()) && $category instanceof WP_Term) {
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

        $posts_query = new WP_Query([
            'post_type' => 'any',
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => [(int) $category->term_id],
                    'include_children' => true,
                ],
            ],
            'posts_per_page' => (int) get_option('posts_per_page'),
            'paged' => $paged,
            'ignore_sticky_posts' => true,
            'suppress_filters' => true,
        ]);

        $used_fallback_query = true;
    }

    ?>
    <div class="container">
    <div class="category-posts">
        <?php
        if ($posts_query instanceof WP_Query && $posts_query->have_posts()) {
            ?>
            <header class="archive-header">
                <?php if ($archive_eyebrow !== '') : ?>
                    <p class="archive-eyebrow"><?php echo esc_html(mb_strtoupper($archive_eyebrow)); ?></p>
                <?php endif; ?>

                <?php if ($archive_title !== '') : ?>
                    <h1 class="page-title"><?php echo esc_html($archive_title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($archive_description)) { ?>
                    <div class="category-description">
                        <?php echo wp_kses_post($archive_description); ?>
                    </div>
                <?php } ?>
            </header>

            <div class="posts-loop">
                <?php
                while ($posts_query->have_posts()) {
                    $posts_query->the_post();
                    $content_words = str_word_count(wp_strip_all_tags(get_the_content()));
                    $reading_minutes = max(1, (int) ceil($content_words / 200));
                    $post_date = get_the_date('F j, Y');
                    $excerpt_source = get_the_excerpt();
                    if (trim((string) $excerpt_source) === '') {
                        $excerpt_source = wp_trim_words(wp_strip_all_tags(get_the_content()), 22, '...');
                    }
                    ?>
                    <article <?php post_class('category-post-card'); ?>>
                        <a class="category-post-card__thumb" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                            <span class="category-post-card__badge"><?php esc_html_e('Đáng chú ý', 'tcp-theme'); ?></span>
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url(TCP_THEME_URI . 'screenshot.png'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" />
                            <?php endif; ?>
                        </a>

                        <div class="category-post-card__content">
                            <h3 class="category-post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <p class="category-post-card__excerpt">
                                <?php echo esc_html(wp_trim_words(wp_strip_all_tags($excerpt_source), 22, '...')); ?>
                            </p>

                            <p class="category-post-card__meta">
                                <?php echo esc_html(sprintf(_n('%s phút đọc', '%s phút đọc', $reading_minutes, 'tcp-theme'), number_format_i18n($reading_minutes))); ?>
                                <span aria-hidden="true">&middot;</span>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html($post_date); ?></time>
                            </p>
                        </div>
                    </article>
                    <?php
                }
                ?>
            </div>

            <!-- Pagination -->
            <?php
            $pagination_total = max(1, (int) $posts_query->max_num_pages);
            if ($pagination_total > 1) :
                $pagination_links = paginate_links([
                    'total'     => $pagination_total,
                    'current'   => max(1, (int) get_query_var('paged'), (int) get_query_var('page')),
                    'type'      => 'list',
                    'prev_text' => '<i class="icon-chevron-left" aria-hidden="true"></i><span>' . esc_html__('Trước', 'tcp-theme') . '</span>',
                    'next_text' => '<span>' . esc_html__('Sau', 'tcp-theme') . '</span><i class="icon-chevron-right" aria-hidden="true"></i>',
                    'mid_size'  => 1,
                    'end_size'  => 1,
                ]);
                if (!empty($pagination_links)) : ?>
                    <nav class="c-pagination" aria-label="<?php esc_attr_e('Phân trang', 'tcp-theme'); ?>">
                        <?php echo wp_kses_post((string) $pagination_links); ?>
                    </nav>
                <?php endif;
            endif; ?>
            <?php
        } else {
            echo '<p>' . esc_html__('No posts found.', 'flatsome') . '</p>';
        }

        if ($used_fallback_query) {
            wp_reset_postdata();
        }
        ?>
    </div>
    </div>
    <?php
}

get_footer();
