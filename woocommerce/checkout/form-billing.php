<?php

/**
 * Checkout billing information form
 *
 * Copy to: yourtheme/woocommerce/checkout/form-billing.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined('ABSPATH') || exit;

$fields = $checkout->get_checkout_fields('billing');

// Keys theo thiết kế (core)
$core_keys = [
	'billing_first_name',
	'billing_email',
	'billing_phone',
	'billing_address_1',
];

// Keys custom (E-learning)
$elearning_keys = [
	'tcp_elearing_email',
	'tcp_elearing_same_as_buyer',
];

// Helper: render field theo key nếu tồn tại
$render_field = static function ($key) use ($fields, $checkout) {
	if (empty($fields[$key])) return;
	woocommerce_form_field($key, $fields[$key], $checkout->get_value($key));
};
?>
<div class="woocommerce-billing-fields c-checkout-billing">
	<h3 class="c-checkout-billing__title">
		<?php esc_html_e('Thông tin người mua', 'woocommerce'); ?>
	</h3>

	<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

	<div class="woocommerce-billing-fields__field-wrapper c-checkout-billing__fields">

		<?php
		// 1) Core fields theo thiết kế
		foreach ($core_keys as $key) {
			$render_field($key);
		}
		?>

		<?php
		// 2) E-learning section (chỉ hiển thị nếu có field được add vào billing group)
		$has_elearing = false;
		foreach ($elearning_keys as $k) {
			if (!empty($fields[$k])) {
				$has_elearing = true;
				break;
			}
		}
		?>

		<?php if ($has_elearing) : ?>
			<section class="c-checkout-billing__elearning">
				<h4 class="c-checkout-billing__elearning-title">
					<?php esc_html_e('Tài khoản TCP E-learning', 'tcp-theme'); ?>
				</h4>

				<p class="c-checkout-billing__elearning-desc">
					<?php esc_html_e('Tài khoản khóa học của  tại Talent Connect+ sẽ được tạo và cung cấp qua email bên dưới.', 'tcp-theme'); ?>
				</p>

				<div class="c-checkout-billing__elearning-fields">

					<!-- Header row: title + checkbox -->
					<div class="c-elearning-head">
						<div class="c-elearning-head__title">
							<?php esc_html_e('Email tài khoản E-learning', 'tcp-theme'); ?>
						</div>

						<div class="c-elearning-head__toggle">
							<?php $render_field('tcp_elearing_same_as_buyer'); ?>
						</div>
					</div>

					<!-- Email input row (bên dưới) -->
					<div class="c-elearning-email js-elearning-email">
						<?php $render_field('tcp_elearing_email'); ?>
					</div>

					<!-- Mirror display (khi tick) -->
					<div class="c-elearning-email-display js-elearning-email-display" style="display:none;">
						<div class="c-elearning-email-display__value">
							<span class="js-elearning-email-value"></span>
						</div>
					</div>

				</div>
			</section>
		<?php endif; ?>


	</div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>

<?php if (!is_user_logged_in() && $checkout->is_registration_enabled()) : ?>
	<div class="woocommerce-account-fields c-checkout-account">
		<?php if (!$checkout->is_registration_required()) : ?>
			<p class="form-row form-row-wide create-account c-checkout-account__toggle">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox c-checkout-account__label">
					<input
						class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox c-checkout-account__checkbox"
						id="createaccount"
						<?php checked((true === $checkout->get_value('createaccount') || (true === apply_filters('woocommerce_create_account_default_checked', false))), true); ?>
						type="checkbox"
						name="createaccount"
						value="1" />
					<span><?php esc_html_e('Create an account?', 'woocommerce'); ?></span>
				</label>
			</p>
		<?php endif; ?>

		<?php do_action('woocommerce_before_checkout_registration_form', $checkout); ?>

		<?php if ($checkout->get_checkout_fields('account')) : ?>
			<div class="create-account c-checkout-account__fields">
				<?php foreach ($checkout->get_checkout_fields('account') as $key => $field) : ?>
					<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>

		<?php do_action('woocommerce_after_checkout_registration_form', $checkout); ?>
	</div>
<?php endif; ?>