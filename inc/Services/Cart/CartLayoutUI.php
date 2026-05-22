<?php

namespace TCP\Theme\Services\Cart;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Dev\HookDebugger;

defined('ABSPATH') || exit;

final class CartLayoutUI
{
    use Singleton;

    protected function init(): void
    {
        add_action('init', [$this, 'register']);
    }

    public function register()
    {
        remove_action('woocommerce_cart_actions', 'flatsome_continue_shopping', 10);
        remove_action('woocommerce_proceed_to_checkout', 'wc_get_pay_buttons', 10);
        remove_action('woocommerce_before_cart_totals', 'flatsome_woocommerce_before_cart_totals', 10);
    }
}
