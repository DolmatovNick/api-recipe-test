<?php

namespace Recapi;

class Environment
{
    protected static $settings = [];

    public static function load(array $settings)
    {
        static::$settings = $settings;
    }

    public static function bind($key, $value)
    {
        if (array_key_exists($key, static::$settings)) {
            throw new \Exception("Key {$key} exists and you try rewrite it");
        }

        static::$settings[$key] = $value;
    }

    public static function get($key)
    {
        if (!array_key_exists($key, static::$settings)) {
            throw new \Exception("No {$key} is bound in the container.");
        }

        return static::$settings[$key];
    }

}