<?php
/**
 * 404 - Render content from WP Page (slug: 404) built with UX Builder.
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

$page_404 = get_page_by_path('404');

if (!$page_404 instanceof WP_Post) {
    // Fallback an toàn nếu chưa có page slug "404"
    get_header();
    echo '<main id="main" role="main"><h1>404</h1></main>';
    get_footer();
    return;
}

// Setup global post để UX Builder shortcodes resolve đúng context
$GLOBALS['post'] = $page_404;
setup_postdata($GLOBALS['post']);

get_header();

echo '<main id="main" role="main">';
echo apply_filters('the_content', $page_404->post_content);
echo '</main>';

get_footer();

wp_reset_postdata();
