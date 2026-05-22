<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          10.1.0
 * @flatsome-version 3.20.0
 */

use TCP\Theme\Services\CourseLoopMetaUI;

defined('ABSPATH') || exit;

$row_classes     = array();
$main_classes    = array();
$sidebar_classes = array();

$auto_refresh  = get_theme_mod('cart_auto_refresh');
$row_classes[] = 'row-large';

if ($auto_refresh) {
	$main_classes[] = 'cart-auto-refresh';
}


$row_classes     = implode(' ', $row_classes);
$main_classes    = implode(' ', $main_classes);
$sidebar_classes = implode(' ', $sidebar_classes);


do_action('woocommerce_before_cart'); ?>
<div class="woocommerce row <?php echo $row_classes; ?>">
	<div class="page-title col">
		<div class="col-inner">
			<h1><?php echo __('Giỏ hàng') ?></h1>
		</div>
	</div>
	<div class="col large-7 pb-0 <?php echo $main_classes; ?>">
		<div class="col-inner">
			<?php wc_print_notices(); ?>
			<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
				<div class="cart-wrapper sm-touch-scroll">

					<?php do_action('woocommerce_before_cart_table'); ?>

					<?php $total_qty = (int) WC()->cart->get_cart_contents_count(); ?>

					<div class="c-cart-head">
						<div class="c-cart-head__qty">
							<span><?php esc_html_e('Số lượng:', 'tcp-theme'); ?></span>
							<strong><?php echo esc_html((string) $total_qty); ?></strong>
						</div>
					</div>

					<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
						<tbody>
							<?php do_action('woocommerce_before_cart_contents'); ?>

							<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
								$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
								$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

								if (!$_product || !$_product->exists() || (int) $cart_item['quantity'] <= 0) continue;
								if (!apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) continue;

								$qty = (int) $cart_item['quantity'];

								$product_permalink = apply_filters(
									'woocommerce_cart_item_permalink',
									$_product->is_visible() ? $_product->get_permalink($cart_item) : '',
									$cart_item,
									$cart_item_key
								);

								$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

								$thumbnail = apply_filters(
									'woocommerce_cart_item_thumbnail',
									$_product->get_image('woocommerce_thumbnail'),
									$cart_item,
									$cart_item_key
								);

								// price
								$regular_unit = (float) $_product->get_regular_price();
								$final_unit   = (float) $_product->get_price();
								$has_discount = ($regular_unit > 0 && $final_unit > 0 && $final_unit < $regular_unit);

								// remove link (giữ chuẩn Woo, chỉ đổi icon)
								$remove_link = apply_filters(
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a role="button" href="%s" class="remove c-cart-item__remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><div class="c-cart-item__remove-icon icon-trash-2"></div></a>',
										esc_url(wc_get_cart_remove_url($cart_item_key)),
										esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
										esc_attr($product_id),
										esc_attr($_product->get_sku())
									),
									$cart_item_key
								);
							?>
								<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
									<td class="product-name" colspan="6">
										<div class="c-cart-item">
											<div class="c-cart-item__thumb">
												<?php if (!$product_permalink) : ?>
													<?php echo wp_kses_post($thumbnail); ?>
												<?php else : ?>
													<a href="<?php echo esc_url($product_permalink); ?>">
														<?php echo wp_kses_post($thumbnail); ?>
													</a>
												<?php endif; ?>
											</div>

											<div class="c-cart-item__body">
												<div class="c-cart-item__info">
													<div class="c-cart-item__info--inner">
														<div class="c-cart-item__title">
															<?php
															if (!$product_permalink) {
																echo wp_kses_post($product_name);
															} else {
																echo wp_kses_post(
																	apply_filters(
																		'woocommerce_cart_item_name',
																		sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()),
																		$cart_item,
																		$cart_item_key
																	)
																);
															}
															?>
															<?php if ($qty > 1): ?>
																<div class="c-cart-item__qty">
																	<span class="c-cart-item__qty-label"><?php esc_html_e('x', 'tcp-theme'); ?></span>
																	<span class="c-cart-item__qty-value"><?php echo esc_html((string) $qty); ?></span>
																</div>
															<?php endif; ?>
														</div>
														<div class="c-cart-item__meta">
															<?php
															// Meta data (variation/custom)
															if (method_exists(CourseLoopMetaUI::instance(), 'render_dot')) {
																CourseLoopMetaUI::instance()->render_dot($_product, $product_id, ['preset' => 'full_icon']);
															}
															?>
														</div>
													</div>
												</div>
												<div class="c-cart-item__actions">
													<?php echo $remove_link; // PHPCS: XSS ok. 
													?>
												</div>
												<div class="c-cart-item__price">
													<?php if ($has_discount) : ?>
														<del><?php echo wp_kses_post(wc_price($regular_unit)); ?></del>
														<ins><?php echo wp_kses_post(wc_price($final_unit)); ?></ins>
													<?php else : ?>
														<ins><?php echo wp_kses_post(wc_price($final_unit)); ?></ins>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>

							<?php do_action('woocommerce_cart_contents'); ?>

							<tr>
								<td colspan="6" class="actions clear">
									<?php do_action('woocommerce_cart_actions'); ?>

									<button type="submit"
										class="button primary mt-0 pull-left small<?php
																					if (fl_woocommerce_version_check('7.0.1')) {
																						echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '');
																					}
																					?>"
										name="update_cart"
										value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
										<?php esc_html_e('Update cart', 'woocommerce'); ?>
									</button>

									<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
								</td>
							</tr>

							<?php do_action('woocommerce_after_cart_contents'); ?>
						</tbody>
					</table>

					<?php do_action('woocommerce_after_cart_table'); ?>
				</div>
			</form>
		</div>
	</div>

	<?php do_action('woocommerce_before_cart_collaterals'); ?>

	<div class="cart-collaterals large-5 col pb-0">
		<div class="col-inner">
			<?php flatsome_sticky_column_open('cart_sticky_sidebar'); ?>

			<div class="cart-sidebar col-inner <?php echo esc_attr($sidebar_classes); ?>">
				<?php
				/**
				 * Cart collaterals hook.
				 *
				 * @hooked woocommerce_cross_sell_display
				 * @hooked woocommerce_cart_totals - 10
				 */
				do_action('woocommerce_cart_collaterals');
				// do_action('woocommerce_proceed_to_checkout');
				?>
				<?php do_action('flatsome_cart_sidebar'); ?>
			</div>

			<?php flatsome_sticky_column_close('cart_sticky_sidebar'); ?>
		</div>
	</div>
</div>

<?php do_action('woocommerce_after_cart'); ?>