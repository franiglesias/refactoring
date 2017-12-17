<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 14/12/17
 * Time: 20:45
 */

class Logger
{
    private static $instance;

    public static function getInstance() : Logger
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function debug($string)
    {
    }
}