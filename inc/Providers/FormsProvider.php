<?php

namespace TCP\Theme\Providers;

use TCP\Theme\Core\ServiceProvider;
use TCP\Theme\Services\Forms\ContactCdpForm;

defined('ABSPATH') || exit;

final class FormsProvider extends ServiceProvider
{
    public function register(): void
    {
        ContactCdpForm::instance();
    }
}
