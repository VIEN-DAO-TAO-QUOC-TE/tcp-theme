<?php

namespace TCP\Theme\Core;

defined('ABSPATH') || exit;

trait Singleton
{
    private static $instance = null;

    final public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static();

            if (method_exists(static::$instance, 'init')) {
                static::$instance->init();
            }
        }
        return static::$instance;
    }

    protected function __construct() {}

    final protected function __clone() {}

    final public function __wakeup()
    {
        // Prevent unserialize
        throw new \Exception('Cannot unserialize singleton');
    }
}
