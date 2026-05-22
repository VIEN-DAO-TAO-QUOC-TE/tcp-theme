<?php

namespace TCP\Theme\Providers;

use TCP\Theme\Core\ServiceProvider;
use TCP\Theme\Services\Assets;

defined('ABSPATH') || exit;

final class AssetsProvider extends ServiceProvider
{
    public function register(): void
    {
        Assets::instance()->register();
    }
}
