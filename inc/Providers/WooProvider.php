<?php

namespace TCP\Theme\Providers;

defined('ABSPATH') || exit;

use TCP\Theme\Core\ServiceProvider;
use TCP\Theme\Services\Woo;
use TCP\Theme\Services\WooNotices;
use TCP\Theme\Services\WooToast;
use TCP\Theme\Services\CourseQuota;
use TCP\Theme\Services\CoursePromoSync;
use TCP\Theme\Services\CourseLoopCardUI;
use TCP\Theme\Services\CourseLoopMetaUI;
use TCP\Theme\Services\CourseLoopCardMediaUI;
// MiniCart
use TCP\Theme\Services\MiniCart\WooCartPolicy;
use TCP\Theme\Services\MiniCart\CourseMiniCartPolicy;
use TCP\Theme\Services\MiniCart\MiniCartTotalsUI;

// Course
use TCP\Theme\Services\Course\CourseData;
use TCP\Theme\Services\Course\CourseAboutUI;
use TCP\Theme\Services\Course\CourseMethodologyUI;
use TCP\Theme\Services\Course\CourseJoinDetailsUI;
use TCP\Theme\Services\Course\CourseFormUI;
use TCP\Theme\Services\Course\CourseSidebarUI;

// Product
use TCP\Theme\Services\Product\ProductLayoutUI;

// Shop
use TCP\Theme\Services\Shop\ShopBreadcrumb;

// Checkout
use TCP\Theme\Services\Checkout\CheckoutLayoutUI;
use TCP\Theme\Services\Checkout\CheckoutCouponBeforePayment;
use TCP\Theme\Services\Checkout\ThankYouService;

// Cart
use TCP\Theme\Services\Cart\CartLayoutUI;

final class WooProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register WooNotices service
        Woo::instance()->register();
        WooNotices::instance()->register();
        WooToast::instance()->register();

        CourseQuota::instance();
        CoursePromoSync::instance();
        CourseLoopCardUI::instance(); /// fix
        CourseLoopMetaUI::instance();
        CourseLoopCardMediaUI::instance();
        CourseData::instance();
        CourseAboutUI::instance();
        CourseMethodologyUI::instance();
        CourseJoinDetailsUI::instance();
        CourseFormUI::instance();
        CourseSidebarUI::instance();

        // WooCartPolicy::instance(); // Tắt trang giỏ hàng

        CourseMiniCartPolicy::instance();
        MiniCartTotalsUI::instance();

        // Checkout
        // CheckoutBlockPolicy::instance();
        CheckoutLayoutUI::instance();
        CheckoutCouponBeforePayment::instance();
        ThankYouService::instance();

        // Cart
        CartLayoutUI::instance();

        // Product
        ProductLayoutUI::instance();

        // Shop
        ShopBreadcrumb::instance();
    }
}
