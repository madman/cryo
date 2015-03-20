<?php

namespace Core;

/**
 * NOTICE: Use this class only if you have no choice.
 * In general, usage of this class is limited.
 */
class ApplicationRegistry
{
    protected static $app;

    public static function set($app)
    {
        self::$app = $app;
    }

    public static function get()
    {
        return self::$app;
    }
}
