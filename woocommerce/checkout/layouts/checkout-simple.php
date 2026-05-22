<?php

/**
 * Checkout simple layout.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.16.0
 */

do_action('get_header', null, array());
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>">

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php do_action('flatsome_after_body_open'); ?>
    <?php wp_body_open(); ?>

    <div id="main-content" class="site-main">

        <div id="main" class="page-checkout-simple <?php flatsome_main_classes(); ?>">
            <div class="cart-header text-center medium-text-center">
                <div class="contianer">
                    <div id="logo" class="logo"><?php get_template_part('template-parts/header/partials/element', 'logo'); ?></div>
                </div>
            </div>

            <div id="content" role="main">
                <div class="container">
                    <?php while (have_posts()) : the_post(); ?>


                        <?php wc_print_notices(); ?>
                        <?php  the_content(); ?>
                        <?php
                        // ob_start();
                        // get_template_part('template-parts/test/checkout');
                        // ob_get_clean();
                        ?>

                    <?php endwhile; // end of the loop. 
                    ?>
                </div>
            </div>

        </div>

        <div class="focused-checkout-footer">
            <?php get_template_part('template-parts/footer/footer', 'absolute'); ?>
        </div>

    </div>

    </div>

    <?php wp_footer(); ?>

</body>

</html>