<?php

namespace TCP\Theme\Core;

defined('ABSPATH') || exit;

abstract class ServiceProvider
{
    abstract public function register(): void;
}
