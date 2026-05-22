<?php
/**
 * Pagination - Loop (WooCommerce archive)
 * Custom override for TCP theme — matches Figma design.
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

if (!isset($total)) {
    global $wp_query;
    $total = ($wp_query instanceof WP_Query) ? (int) $wp_query->max_num_pages : 1;
}
if (!isset($current)) {
    $current = max(1, (int) get_query_var('paged'));
}
if ((int) $total <= 1) {
    return;
}

$prev_text = '<i class="icon-chevron-left" aria-hidden="true"></i><span>' . esc_html__('Trước', 'tcp-theme') . '</span>';
$next_text = '<span>' . esc_html__('Sau', 'tcp-theme') . '</span><i class="icon-chevron-right" aria-hidden="true"></i>';

$links = paginate_links([
    'total'     => (int) $total,
    'current'   => (int) $current,
    'type'      => 'list',
    'prev_text' => $prev_text,
    'next_text' => $next_text,
    'mid_size'  => 1,
    'end_size'  => 1,
]);

if (empty($links)) {
    return;
}
?>
<nav class="c-pagination" aria-label="<?php esc_attr_e('Phân trang', 'tcp-theme'); ?>">
    <?php echo wp_kses_post((string) $links); ?>
</nav>
