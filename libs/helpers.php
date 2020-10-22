<?php

use Spool\Config\Env;
use Spool\Config\Config;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key     要获取的键名
     * @param mixed  $default 默认值
     * 
     * @return mixed
     */
    function env($key, $default = null)
    {
        return Env::getInstance()->get($key, $default);
    }
}

if (!function_exists('config')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key     要获取的键名
     * @param mixed  $default 默认值
     * 
     * @return mixed
     */
    function config($key, $default = null)
    {
        return Config::getInstance()->get($key, $default);
    }
}

if (!function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param string|object $class 要查看的类名或对象
     * 
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}
