<?php

/**
 * Product.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.19.9
 */


defined('ABSPATH') || exit;

global $product;
if (!$product) return;

use TCP\Theme\Services\Course\CourseData;
use TCP\Theme\Services\Course\CourseCurriculumUI;
use TCP\Theme\Services\Course\CourseTrainerUI;
use TCP\Theme\Services\Course\CourseHeroCoverUI;
use TCP\Theme\Services\Course\CoursePainPointsUI;
use TCP\Theme\Services\Course\CourseAudienceUI;
use TCP\Theme\Services\Course\CourseOutcomesUI;
use TCP\Theme\Services\Course\CourseMicroUI;
use TCP\Theme\Services\Course\CourseTestimonialsUI;
use TCP\Theme\Services\Course\CourseFaqUI;
use TCP\Theme\Services\Course\CourseSidebarUI;
use TCP\Theme\Services\Course\CourseStickyHeaderUI;
use TCP\Theme\Services\Course\CourseAboutUI;
use TCP\Theme\Services\Course\CourseMethodologyUI;
use TCP\Theme\Services\Course\CourseJoinDetailsUI;
use TCP\Theme\Services\Course\CourseFormUI;

$data = CourseData::instance()->get($product->get_id());

// echo "<pre>";
// var_dump($data);
// echo "</pre>";
// die;

CourseStickyHeaderUI::instance()->render($product, $data);

?>
<div class="product-container">
    <div class="product-main">
        <?php
        if (function_exists("rank_math_the_breadcrumbs")) :
        ?>
            <div class="rank_math_the_breadcrumbs container">
                <?php rank_math_the_breadcrumbs(); ?>
            </div>
        <?php
        endif;
        ?>
        <div class="row content-row mb-0 c-course__body">

            <div class="product-gallery c-course__content col large-<?php echo get_theme_mod('product_image_width', '6'); ?>">
                <div class="col-inner">
                    <?php
                    # CourseHeroCoverUI
                    CourseHeroCoverUI::instance()->render($product, $data);

                    # CourseTrainerUI
                    CourseTrainerUI::instance()->render($product, $data);

                    # CoursePainPointsUI
                    CoursePainPointsUI::instance()->render($product, $data);

                    # CourseAudienceUI
                    CourseAudienceUI::instance()->render($product, $data);

                    # CourseOutcomesUI
                    CourseOutcomesUI::instance()->render($product, $data);

                    # CourseAboutUI (#1 - Về khóa học)
                    CourseAboutUI::instance()->render($product, $data);

                    # CourseCurriculumUI
                    CourseCurriculumUI::instance()->render($product, $data);

                    # CourseMethodologyUI (#2 - Phương pháp UPWARDS)
                    CourseMethodologyUI::instance()->render($product, $data);

                    # CourseMicroUI
                    CourseMicroUI::instance()->render($product, $data);

                    # CourseJoinDetailsUI (#3 - Cách thức tham gia)
                    CourseJoinDetailsUI::instance()->render($product, $data);

                    # CourseTestimonialsUI
                    CourseTestimonialsUI::instance()->render($product, $data);

                    # CourseFaqUI
                    CourseFaqUI::instance()->render($product, $data);

                    ?>
                    <?php
                    /**
                     * woocommerce_before_single_product_summary hook
                     *
                     * @hooked woocommerce_show_product_sale_flash - 10
                     * @hooked woocommerce_show_product_images - 20
                     */
                    // do_action('woocommerce_before_single_product_summary');
                    ?>
                </div>
            </div>

            <div class="product-info c-course__sidebar summary col-fit col entry-summary <?php flatsome_product_summary_classes(); ?>">
                <div class="is-sticky-column">
                    <div class="is-sticky-column__inner">
                        <div class="col-inner">
                            <?php CourseSidebarUI::instance()->render($product, $data); ?>

                            <?php CourseFormUI::instance()->render($product, $data); ?>

                            <?php
                            /**
                             * woocommerce_single_product_summary hook
                             *
                             * @hooked woocommerce_template_single_title - 5
                             * @hooked woocommerce_template_single_rating - 10
                             * @hooked woocommerce_template_single_price - 10
                             * @hooked woocommerce_template_single_excerpt - 20
                             * @hooked woocommerce_template_single_add_to_cart - 30
                             * @hooked woocommerce_template_single_meta - 40
                             * @hooked woocommerce_template_single_sharing - 50
                             */
                            // do_action('woocommerce_single_product_summary');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="product-footer">
        <div class="container">
            <?php
            /**
             * woocommerce_after_single_product_summary hook
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action('woocommerce_after_single_product_summary');
            ?>
        </div>
    </div>
</div>