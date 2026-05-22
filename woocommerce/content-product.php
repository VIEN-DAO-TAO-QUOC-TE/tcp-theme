<?php

/**
 * The template for displaying product content within loops
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          9.4.0
 * @flatsome-version 3.20.0
 */

defined('ABSPATH') || exit;

use TCP\Theme\Services\CourseQuota;
use TCP\Theme\Services\CourseLoopCardUI;
use TCP\Theme\Services\CourseLoopCardMediaUI;

global $product;

// Ensure product is valid + visible
if (!$product || !is_a($product, WC_Product::class) || !$product->is_visible()) {
    return;
}

$product_id = (int) $product->get_id();
$permalink  = get_the_permalink($product_id);

// Services (avoid multiple instance() calls)
$mediaUI = CourseLoopCardMediaUI::instance();
$cardUI  = CourseLoopCardUI::instance();
$quota   = CourseQuota::instance();

// Background attribute + class
$bg_attr  = $mediaUI->col_inner_bg_attr($product_id); // returns: style="--tcp-course-card-bg: url('...');"
$bg_class = $bg_attr ? ' has-course-card-bg' : '';

// Stock status
$out_of_stock = !$product->is_in_stock();

// Wrapper classes
$classes = ['product-small', 'col', 'has-hover'];
if ($out_of_stock) {
    $classes[] = 'out-of-stock';
}

// Validate purchase UI once
$can_purchase_ui = $cardUI->can_render_purchase_ui($product);
?>
<div <?php wc_product_class($classes, $product); ?>>
    <div class="col-inner<?php echo esc_attr($bg_class); ?>" <?php echo $bg_attr ? ' ' . $bg_attr : ''; ?>>
        <?php do_action('woocommerce_before_shop_loop_item'); ?>

        <div class="product-small box <?php echo esc_attr(flatsome_product_box_class()); ?>">
            <div class="box-image">
                <div class="<?php echo esc_attr(flatsome_product_box_image_class()); ?>">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <?php
                        /**
                         * @hooked woocommerce_get_alt_product_thumbnail - 11
                         * @hooked woocommerce_template_loop_product_thumbnail - 10
                         */
                        do_action('flatsome_woocommerce_shop_loop_images');
                        ?>
                    </a>
                </div>

                <div class="image-tools is-small top right show-on-hover">
                    <?php do_action('flatsome_product_box_tools_top'); ?>
                </div>

                <div class="image-tools is-small hide-for-small bottom left show-on-hover">
                    <?php do_action('flatsome_product_box_tools_bottom'); ?>
                </div>

                <div class="image-tools <?php echo esc_attr(flatsome_product_box_actions_class()); ?>">
                    <?php do_action('flatsome_product_box_actions'); ?>
                </div>

                <?php do_action('tcp_course_card_badges'); ?>

                <?php if ($out_of_stock) : ?>
                    <div class="out-of-stock-label"><?php esc_html_e('Out of stock', 'woocommerce'); ?></div>
                <?php endif; ?>
            </div>

            <div class="box-text <?php echo esc_attr(flatsome_product_box_text_class()); ?>">
                <div class="box-text__inner">
                    <div class="box-text__group">

                        <div class="box-text__top"></div>

                        <div class="box-text__body">
                            <div class="title-wrapper">
                                <?php do_action('woocommerce_shop_loop_item_title'); ?>
                            </div>

                            <?php
                            // Your custom meta hook (badges + meta row)
                            do_action('woocommerce_shop_loop_item_meta');
                            ?>
                        </div>

                        <div class="box-text__footer">
                            <?php if ($can_purchase_ui) : ?>
                                <div class="price-wrapper">
                                    <?php do_action('woocommerce_after_shop_loop_item_title'); ?>
                                </div>

                                <?php
                                echo $quota->render_loop_pill($product_id, [
                                    'mode' => 'used',
                                ]);
                                ?>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div class="box-text__cta-placeholder">
                        <div class="box-text__after">
                            <?php do_action('flatsome_product_box_after'); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php do_action('woocommerce_after_shop_loop_item'); ?>
    </div>
</div>
<?php /* empty PHP to avoid whitespace */ ?>