<?php

namespace TCP\Theme\Core;

defined('ABSPATH') || exit;

final class Autoloader
{
    private const PREFIX = 'TCP\\Theme\\';
    private const BASE_DIR = __DIR__ . '/../'; // points to /inc/

    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'autoload'], true, true);
    }

    private static function autoload(string $class): void
    {
        // Only load our namespace
        if (strpos($class, self::PREFIX) !== 0) {
            return;
        }

        // Remove prefix
        $relative = substr($class, strlen(self::PREFIX)); // e.g. "Services\\Assets"
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);

        $file = realpath(self::BASE_DIR) . DIRECTORY_SEPARATOR . $relative . '.php';

        if (is_readable($file)) {
            require_once $file;
        }
    }
}
