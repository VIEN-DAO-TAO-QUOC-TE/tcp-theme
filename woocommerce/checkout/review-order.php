<?php

/**
 * Review order table
 *
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

$cart = WC()->cart;
if (!$cart) return;

$get_cart = $cart->get_cart();

/**
 * 1) Tổng qty đúng nghĩa "Số lượng"
 * - Woo chuẩn: tổng quantity của tất cả dòng
 */
$total_qty = (int) $cart->get_cart_contents_count();

/**
 * 2) Saving total theo LINE (nhất quán với subtotal/total)
 * saving_line = (regular_unit * qty) - line_total
 * - line_total: thành tiền sau giảm/coupon (chưa tax)
 */
$saving_total = 0.0;

foreach ($get_cart as $cart_item_key => $cart_item) {
    $_product = $cart_item['data'] ?? null;
    if (!$_product || !is_a($_product, 'WC_Product')) continue;

    $qty = (int) ($cart_item['quantity'] ?? 0);
    if ($qty <= 0) continue;

    $regular_unit = (float) $_product->get_regular_price();

    // Nếu không có regular -> coi như không có tiết kiệm theo kiểu "giá gốc"
    if ($regular_unit <= 0) continue;

    $regular_line = $regular_unit * $qty;

    $line_total = (float) ($cart_item['line_total'] ?? 0.0); // after coupon allocations
    if ($line_total <= 0) continue;

    $diff = $regular_line - $line_total;
    if ($diff > 0) $saving_total += $diff;
}

?>
<table class="shop_table woocommerce-checkout-review-order-table">
    <thead>
        <tr>
            <th class="product-name"><?php esc_html_e('Số lượng:', 'woocommerce'); ?></th>
            <th class="product-total"><?php echo esc_html((string) $total_qty); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach ($get_cart as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if (!$_product || !$_product->exists() || (int) ($cart_item['quantity'] ?? 0) <= 0) continue;
            if (!apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) continue;

            $qty = (int) $cart_item['quantity'];

            // Thumbnail
            $thumbnail = apply_filters(
                'woocommerce_cart_item_thumbnail',
                $_product->get_image('woocommerce_thumbnail'),
                $cart_item,
                $cart_item_key
            );

            // Name
            $product_name = apply_filters(
                'woocommerce_cart_item_name',
                $_product->get_name(),
                $cart_item,
                $cart_item_key
            );

            // Meta
            $item_data = wc_get_formatted_cart_item_data($cart_item);

            /**
             * 3) PRICE hiển thị theo LINE (nhất quán với totals)
             * - regular_line = regular_unit * qty (để gạch)
             * - final_line   = line_total (sau giảm/coupon)
             */
            $regular_unit = (float) $_product->get_regular_price();
            $regular_line = ($regular_unit > 0) ? ($regular_unit * $qty) : 0.0;

            $line_total    = (float) ($cart_item['line_total'] ?? 0.0);
            $line_subtotal = (float) ($cart_item['line_subtotal'] ?? 0.0);

            // Fallback nếu line_total chưa được tính (hiếm)
            if ($line_total <= 0) {
                $line_total = (float) wc_get_price_excluding_tax($_product, ['qty' => $qty]);
            }

            // Có discount nếu regular_line > line_total (ưu tiên regular)
            // Nếu regular không có thì fallback: line_subtotal vs line_total (coupon)
            $has_discount = false;
            $regular_display = 0.0;

            if ($regular_line > 0 && $line_total > 0 && $line_total < $regular_line) {
                $has_discount = true;
                $regular_display = $regular_line;
            } elseif ($line_subtotal > 0 && $line_total > 0 && $line_total < $line_subtotal) {
                // trường hợp không có regular nhưng có coupon
                $has_discount = true;
                $regular_display = $line_subtotal;
            }
        ?>
            <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <td class="product-name">
                    <div class="c-ro-item">
                        <div class="c-ro-item__thumb">
                            <?php echo wp_kses_post($thumbnail); ?>
                        </div>

                        <div class="c-ro-item__content">
                            <div class="c-ro-item__title">
                                <?php echo wp_kses_post($product_name); ?>
                            </div>

                            <?php
                            if ($qty > 1) {
                                echo apply_filters(
                                    'woocommerce_checkout_cart_item_quantity',
                                    ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty) . '</strong>',
                                    $cart_item,
                                    $cart_item_key
                                ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            }

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
                    <?php if ($has_discount) : ?>
                        <del><?php echo wp_kses_post(wc_price($regular_display)); ?></del>
                        <ins><?php echo wp_kses_post(wc_price($line_total)); ?></ins>
                    <?php else : ?>
                        <?php echo wp_kses_post(wc_price($line_total)); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php
        }

        do_action('woocommerce_review_order_after_cart_contents');
        ?>
        <tr class="c-divider">
            <td colspan="2"></td>
        </tr>
        <tr class="c-divider-gap">
            <td colspan="2"></td>
        </tr>
    </tbody>

    <tfoot>
        <tr class="cart-subtotal">
            <th><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            <td><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach ($cart->get_coupons() as $code => $coupon) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ($cart->needs_shipping() && $cart->show_shipping()) : ?>
            <?php do_action('woocommerce_review_order_before_shipping'); ?>
            <?php wc_cart_totals_shipping_html(); ?>
            <?php do_action('woocommerce_review_order_after_shipping'); ?>
        <?php endif; ?>

        <?php foreach ($cart->get_fees() as $fee) : ?>
            <tr class="fee">
                <th><?php echo esc_html($fee->name); ?></th>
                <td><?php wc_cart_totals_fee_html($fee); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && ! $cart->display_prices_including_tax()) : ?>
            <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                <?php foreach ($cart->get_tax_totals() as $code => $tax) : ?>
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

        <?php if ($saving_total > 0) : ?>
            <tr class="cart-savings">
                <th><?php echo esc_html__('Bạn đã tiết kiệm', 'tcp-theme'); ?></th>
                <td><?php echo wp_kses_post(wc_price($saving_total)); ?></td>
            </tr>
        <?php endif; ?>

        <?php do_action('woocommerce_review_order_after_order_total'); ?>
    </tfoot>
</table>