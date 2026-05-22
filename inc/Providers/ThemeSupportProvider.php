<?php

namespace TCP\Theme\Providers;

use TCP\Theme\Core\ServiceProvider;
use TCP\Theme\Services\ThemeSupport;
use TCP\Theme\Services\Ux\UxAboutPage;
use TCP\Theme\Services\Ux\UxButton;
use TCP\Theme\Services\Ux\UxHomePage;
use TCP\Theme\Services\Ux\UxReviews;
use TCP\Theme\Services\Ux\UxTrainers;

defined('ABSPATH') || exit;

final class ThemeSupportProvider extends ServiceProvider
{
    public function register(): void
    {
        ThemeSupport::instance()->register();

        //UX Builder
        UxAboutPage::instance();
        UxButton::instance();
        UxHomePage::instance();
        UxReviews::instance();
        UxTrainers::instance();
    }
}
