<?php

namespace TCP\Theme;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Providers\AssetsProvider;
use TCP\Theme\Providers\FormsProvider;
use TCP\Theme\Providers\ThemeSupportProvider;
use TCP\Theme\Providers\WooProvider;

defined('ABSPATH') || exit;

final class Theme
{
    use Singleton;

    /** @var array<object> */
    private $providers = [];

    public function boot(): void
    {
        // Config (nếu cần)
        require_once __DIR__ . '/Config/config.php';

        $this->register_providers();
        $this->register_hooks();
    }

    private function register_providers(): void
    {
        $this->providers = [
            new ThemeSupportProvider(),
            new AssetsProvider(),
            new WooProvider(),
            new FormsProvider(),
        ];

        foreach ($this->providers as $provider) {
            $provider->register();
        }
    }

    private function register_hooks(): void
    {
        // chỗ này để add action/filter ở cấp theme nếu muốn
    }
}
