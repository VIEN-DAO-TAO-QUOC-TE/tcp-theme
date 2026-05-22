<?php

namespace TCP\Theme\Services\Checkout;

use WC_Order;
use WC_Product;

defined('ABSPATH') || exit;

final class ThankYouService
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Entry render for Thank You layout.
     */
    public function render(WC_Order $order): void
    {
        if ($order->has_status('failed')) {
            $this->render_failed($order);
            return;
        }

        echo '<div class="c-thankyou__grid">';

        // LEFT
        echo '<div class="c-thankyou__left">';
        $this->render_success_card($order);
        echo '<div class="c-thankyou-card c-thankyou-card__group">';
        $this->render_buyer_card($order);
        echo '<div class="c-thankyou-card__divider"></div>';
        $this->render_elearing_card($order);
        echo '</div>';
        echo '</div>';

        // RIGHT
        echo '<div class="c-thankyou__right">';
        $this->render_order_summary_card($order);
        echo '</div>';

        echo '</div>';
    }

    private function render_failed(WC_Order $order): void
    {
        echo '<div class="c-thankyou__grid">';
        echo '<div class="c-thankyou__left">';

        echo '<div class="c-thankyou-card c-thankyou-failed">';
        echo '<h2 class="c-thankyou-failed__title">' . esc_html__('Thanh toán chưa thành công', 'tcp-theme') . '</h2>';
        echo '<p class="c-thankyou-failed__desc">' . esc_html__('Giao dịch bị từ chối từ ngân hàng/đơn vị thanh toán. Vui lòng thử lại.', 'tcp-theme') . '</p>';

        echo '<div class="c-thankyou-failed__actions">';
        echo '<a class="button pay" href="' . esc_url($order->get_checkout_payment_url()) . '">' . esc_html__('Thanh toán lại', 'tcp-theme') . '</a>';

        if (is_user_logged_in()) {
            echo '<a class="button" href="' . esc_url(wc_get_page_permalink('myaccount')) . '">' . esc_html__('Tài khoản của tôi', 'tcp-theme') . '</a>';
        }
        echo '</div>';

        echo '</div>'; // card
        echo '</div>'; // left

        echo '<div class="c-thankyou__right">';
        $this->render_order_summary_card($order);
        echo '</div>';

        echo '</div>'; // grid
    }

    private function render_success_card(WC_Order $order): void
    {
        $success_icon = TCP_THEME_URI . 'assets/images/icons/success.svg';
        $home = home_url('/');

        echo '<div class="c-thankyou-card c-thankyou-success">';
        echo '<div class="c-thankyou-success__icon" aria-hidden="true"><img src="' . $success_icon . '"></div>';

        echo '<h2 class="c-thankyou-success__title">' . esc_html__('Thanh toán thành công', 'tcp-theme') . '</h2>';

        echo '<p class="c-thankyou-success__desc">';
        echo esc_html__('Chúng tôi sẽ gửi cho bạn các email sau: xác nhận thanh toán, thông tin đăng nhập tài khoản khóa học. Hãy kiểm tra hộp thư đến của bạn để không bỏ lỡ thông tin này!', 'tcp-theme');
        echo '</p>';

        echo '<a class="c-btn c-btn--primary c-thankyou-success__btn primary is-small mb-0 button " href="' . esc_url($home) . '">';
        echo esc_html__('Trở về trang chủ', 'tcp-theme');
        echo '</a>';

        echo '</div>';
    }

    private function render_buyer_card(WC_Order $order): void
    {
        $name  = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
        $email = $order->get_billing_email();
        $phone = $order->get_billing_phone();
        $addr  = $this->format_address_inline($order->get_formatted_billing_address());

        echo '<div class="c-thankyou-card__item c-thankyou-buyer">';
        echo '<h3 class="c-thankyou-card__title">' . esc_html__('Thông tin người mua', 'tcp-theme') . '</h3>';

        echo '<ul class="c-info-list">';
        $this->info_row(__('Họ và tên:', 'tcp-theme'), $name ?: '—');
        $this->info_row(__('Email:', 'tcp-theme'), $email ?: '—');
        $this->info_row(__('Số điện thoại:', 'tcp-theme'), $phone ?: '—');
        $this->info_row(__('Địa chỉ:', 'tcp-theme'), $addr ?: '—');
        echo '</ul>';

        echo '</div>';
    }

    private function render_elearing_card(WC_Order $order): void
    {
        // Meta key theo bạn đang dùng
        $elearning_email = (string) $order->get_meta('tcp_elearing_email');

        // Nếu không có, vẫn có thể fallback lấy billing email (tùy bạn muốn strict hay not)
        if ($elearning_email === '') {
            // Nếu bạn muốn “chỉ hiện khi có meta”, hãy return luôn:
            // return;

            $elearning_email = (string) $order->get_billing_email();
        }

        echo '<div class="c-thankyou-card__item c-thankyou-elearning">';
        echo '<h3 class="c-thankyou-card__title">' . esc_html__('Tài khoản TCP E-learning', 'tcp-theme') . '</h3>';

        echo '<p class="c-thankyou-elearning__desc">';
        echo esc_html__('Tài khoản khóa học của bạn tại Talent Connect+ sẽ được tạo và cung cấp qua email bên dưới.', 'tcp-theme');
        echo '</p>';

        echo '<div class="c-thankyou-elearning__row">';
        echo '<span class="c-thankyou-elearning__label">' . esc_html__('Email tài khoản E-learning:', 'tcp-theme') . '</span>';
        echo '<strong class="c-thankyou-elearning__value">' . esc_html($elearning_email) . '</strong>';
        echo '</div>';

        echo '</div>';
    }

    private function render_order_summary_card(WC_Order $order): void
    {
        $order_number    = $order->get_order_number();
        $transaction_id  = $order->get_transaction_id();
        $date_created    = $order->get_date_created() ? wc_format_datetime($order->get_date_created()) : '';
        $payment_method  = $order->get_payment_method_title();
        $total_formatted = $order->get_formatted_order_total();

        echo '<div class="c-thankyou-card c-thankyou-summary">';

        echo '<h3 class="c-thankyou-card__title">' . esc_html__('Thông tin đơn hàng', 'tcp-theme') . '</h3>';

        echo '<ul class="c-order-meta">';
        $this->meta_row(__('Mã đơn hàng:', 'tcp-theme'), '#' . $order_number);
        $this->meta_row(__('Mã giao dịch:', 'tcp-theme'), $transaction_id ?: '—');
        $this->meta_row(__('Ngày khởi tạo:', 'tcp-theme'), $date_created ?: '—');
        if (!empty($payment_method)) {
            $this->meta_row(__('Thanh toán:', 'tcp-theme'), $payment_method);
        }
        echo '</ul>';
        echo '<div class="c-thankyou-card__divider"></div>';

        echo '<div class="c-order-products">';
        foreach ($order->get_items() as $item) {
            /** @var WC_Product|null $product */
            $product = $item->get_product();
            $qty     = (int) $item->get_quantity();
            $name    = $item->get_name();

            // line total: chỉ tiền hàng (không ship/fee). Nếu bạn muốn “giá hiển thị theo sản phẩm x qty” giữ get_total()
            $line_total = (float) $item->get_total();

            echo '<div class="c-order-product">';

            echo '<div class="c-order-product__thumb">';
            if ($product instanceof WC_Product) {
                echo $product->get_image('thumbnail');
            } else {
                echo '<span class="c-order-product__thumb--placeholder"></span>';
            }
            echo '</div>';

            echo '<div class="c-order-product__info">';
            echo '<div class="c-order-product__name">';
            echo  esc_html($name);
            echo '<span class="c-order-product__qty">' . sprintf(esc_html__('x %d', 'tcp-theme'), $qty) . '</span>';
            echo '</div>';

            echo '<div class="c-order-product__sub">';
            
            echo '<span class="c-order-product__price">' . wp_kses_post(wc_price($line_total)) . '</span>';
            echo '</div>';

            echo '</div>'; // info
            echo '</div>'; // product
        }
        echo '</div>'; // products

        echo '<div class="c-thankyou-card__divider"></div>';

        echo '<div class="c-order-total">';
        echo '<span class="c-order-total__label">' . esc_html__('Đã thanh toán', 'tcp-theme') . '</span>';
        echo '<strong class="c-order-total__value">' . wp_kses_post($total_formatted) . '</strong>';
        echo '</div>';

        echo '</div>'; // card
    }

    /* -----------------------------
	 * Helpers
	 * ----------------------------- */

    private function info_row(string $label, string $value): void
    {
        echo '<li class="c-info-list__item">';
        echo '<span class="c-info-list__label">' . esc_html($label) . '</span>';
        echo '<strong class="c-info-list__value">' . esc_html($value) . '</strong>';
        echo '</li>';
    }

    private function meta_row(string $label, string $value): void
    {
        echo '<li class="c-order-meta__item">';
        echo '<span class="c-order-meta__label">' . esc_html($label) . '</span>';
        echo '<strong class="c-order-meta__value">' . esc_html($value) . '</strong>';
        echo '</li>';
    }

    private function format_address_inline(string $address_html): string
    {
        // get_formatted_billing_address trả HTML <br/>. Convert thành text 1 dòng nhẹ nhàng.
        $txt = wp_strip_all_tags($address_html);
        $txt = preg_replace('/\s+/', ' ', (string) $txt);
        return trim((string) $txt);
    }
}
