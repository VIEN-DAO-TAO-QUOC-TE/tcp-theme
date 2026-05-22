<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
$get_cart = WC()->cart->get_cart();
?>
<table class="shop_table woocommerce-checkout-review-order-table">
    <thead>
        <tr>
            <th class="product-name"><?php esc_html_e('Số lượng:', 'woocommerce'); ?></th>
            <th class="product-total"><?php esc_html_e(count($get_cart), 'woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach ($get_cart as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) {
                continue;
            }

            if (!apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                continue;
            }

            $qty = (int) $cart_item['quantity'];

            // --- IMAGE (đúng chuẩn Woo filter) ---
            $thumbnail = apply_filters(
                'woocommerce_cart_item_thumbnail',
                $_product->get_image('woocommerce_thumbnail'),
                $cart_item,
                $cart_item_key
            );

            // --- NAME (đúng chuẩn Woo filter) ---
            $product_name = apply_filters(
                'woocommerce_cart_item_name',
                $_product->get_name(),
                $cart_item,
                $cart_item_key
            );

            // --- META (biến thể / custom data) ---
            $item_data = wc_get_formatted_cart_item_data($cart_item);

            // --- PRICE: hiển thị trước/sau giảm theo LINE (qty) ---
            // line_total / line_subtotal luôn phản ánh "trước giảm" vs "sau giảm" (không gồm tax)
            $line_subtotal = isset($cart_item['line_subtotal']) ? (float) $cart_item['line_subtotal'] : 0.0; // trước giảm
            $line_total    = isset($cart_item['line_total']) ? (float) $cart_item['line_total'] : 0.0;       // sau giảm

            $has_discount = ($line_subtotal > 0 && $line_total > 0 && $line_total < $line_subtotal);

            // Format tiền theo currency hiện tại
            $subtotal_html = wc_price($line_subtotal);
            $total_html    = wc_price($line_total);

            // Fallback subtotal chuẩn của Woo (an toàn cho nhiều loại product)
            $fallback_subtotal_html = apply_filters(
                'woocommerce_cart_item_subtotal',
                WC()->cart->get_product_subtotal($_product, $qty),
                $cart_item,
                $cart_item_key
            );
        ?>
            <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <td class="product-name">
                    <div class="c-ro-item" style="display:flex;gap:14px;align-items:flex-start;">
                        <div class="c-ro-item__thumb" style="flex:0 0 auto;">
                            <?php echo wp_kses_post($thumbnail); ?>
                        </div>

                        <div class="c-ro-item__content" style="flex:1 1 auto;min-width:0;">
                            <div class="c-ro-item__title" style="font-weight:600;">
                                <?php echo wp_kses_post($product_name); ?>
                            </div>

                            <?php
                            // qty giữ nguyên class cũ Woo để JS/plugin không bị ảnh hưởng
                            echo apply_filters(
                                'woocommerce_checkout_cart_item_quantity',
                                ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty) . '</strong>',
                                $cart_item,
                                $cart_item_key
                            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>

                            <?php if (!empty($item_data)) : ?>
                                <div class="c-ro-item__meta">
                                    <?php echo wp_kses_post($item_data); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>

                <td class="product-total">
                    <?php echo wp_kses_post($_product->get_price_html()); ?>
                    <?php if ($has_discount) : ?>
                        <div class="c-ro-price">
                            <div class="c-ro-price__regular" style="text-decoration:line-through;opacity:.6;">
                                <?php echo wp_kses_post($subtotal_html); ?>
                            </div>
                            <div class="c-ro-price__sale" style="font-weight:700;">
                                <?php echo wp_kses_post($total_html); ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="c-ro-price">
                            <?php echo wp_kses_post($fallback_subtotal_html); ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php
        }

        do_action('woocommerce_review_order_after_cart_contents');
        ?>
    </tbody>

    <tfoot>

        <tr class="cart-subtotal">
            <th><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            <td><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

            <?php do_action('woocommerce_review_order_before_shipping'); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action('woocommerce_review_order_after_shipping'); ?>

        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <tr class="fee">
                <th><?php echo esc_html($fee->name); ?></th>
                <td><?php wc_cart_totals_fee_html($fee); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && ! WC()->cart->display_prices_including_tax()) : ?>
            <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited 
                ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                        <th><?php echo esc_html($tax->label); ?></th>
                        <td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="tax-total">
                    <th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                    <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action('woocommerce_review_order_before_order_total'); ?>

        <tr class="order-total">
            <th><?php esc_html_e('Total', 'woocommerce'); ?></th>
            <td><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

        <?php do_action('woocommerce_review_order_after_order_total'); ?>

    </tfoot>
</table>