<?php

/**
 * Category title (override for Shop + Category tabs)
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.18.4
 */

defined('ABSPATH') || exit;

$classes = [
    'shop-page-title',
    'category-page-title',
    'page-title',
    flatsome_header_title_classes(false),
];

if (get_theme_mod('content_color') === 'dark') {
    $classes[] = 'dark';
}

/**
 * Title logic:
 * - Shop page: lấy title của trang Shop (page đang set trong WooCommerce)
 * - Product category archive: title là tên category
 * - Fallback: giữ hành vi cũ của Flatsome qua hooks
 */
$shop_page_id    = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
$shop_page_title = $shop_page_id && $shop_page_id > 0 ? get_the_title($shop_page_id) : '';

$is_shop_page     = function_exists('is_shop') ? is_shop() : false;
$is_product_cat   = function_exists('is_product_category') ? is_product_category() : false;

// Active term id nếu đang ở trang category
$active_term_id = 0;
if ($is_product_cat) {
    $qo = get_queried_object();
    if (!empty($qo) && !empty($qo->term_id)) {
        $active_term_id = (int) $qo->term_id;
    }
}

// Lấy danh sách category (top-level). Có thể bỏ parent=0 nếu muốn hiện cả cấp con.
$category_terms = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
]);

$show_tabs = ($is_shop_page || $is_product_cat) && !is_wp_error($category_terms) && !empty($category_terms);

$shop_url = ($shop_page_id && $shop_page_id > 0) ? get_permalink($shop_page_id) : home_url('/');
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="page-title-inner flex-row medium-flex-wrap container">

        <div class="flex-col flex-grow medium-text-center">
            <?php if ($is_shop_page && $shop_page_title) : ?>
                <h1 class="shop-page-title__heading"><?php echo esc_html($shop_page_title); ?></h1>
            <?php elseif ($is_product_cat) : ?>
                <h1 class="shop-page-title__heading"><?php single_term_title(); ?></h1>
            <?php else : ?>
                <?php do_action('flatsome_category_title'); ?>
            <?php endif; ?>

            <?php if ($show_tabs) : ?>
                <nav class="shop-cat-tabs" aria-label="<?php echo esc_attr__('Product categories', 'woocommerce'); ?>">
                    <ul class="shop-cat-tabs__list">

                        <?php
                        // Tab "Tất cả"
                        $all_active = $is_shop_page && !$is_product_cat;
                        ?>
                        <li class="shop-cat-tabs__item">
                            <a class="shop-cat-tabs__link <?php echo $all_active ? 'is-active' : ''; ?>"
                                href="<?php echo esc_url($shop_url); ?>">
                                <?php echo esc_html__('Tất cả', 'tcp-theme'); ?>
                            </a>
                        </li>

                        <?php foreach ($category_terms as $term) : ?>
                            <?php
                            $term_link = get_term_link($term);
                            if (is_wp_error($term_link)) continue;

                            $is_active = $active_term_id === (int) $term->term_id;
                            ?>
                            <li class="shop-cat-tabs__item">
                                <a class="shop-cat-tabs__link <?php echo $is_active ? 'is-active' : ''; ?>"
                                    href="<?php echo esc_url($term_link); ?>">
                                    <?php echo esc_html($term->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <div class="flex-col medium-text-center">
            <?php #do_action('flatsome_category_title_alt'); 
            ?>
        </div>

    </div>
</div>