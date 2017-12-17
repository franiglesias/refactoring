<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 14/12/17
 * Time: 20:47
 */

class Providers
{
    public const PROVIDER1 = 1;
    public const PROVIDER2 = 2;
    public const PROVIDER3 = 3;
    public const PROVIDER4 = 4;
    public const PROVIDER5 = 5;
    public const PROVIDER6 = 6;

    public const ASSOCIATED = [
        self::PROVIDER3,
        self::PROVIDER4
    ];

    public static function isProvider1($providerCode) : bool
    {
        return self::PROVIDER1 === $providerCode;
    }

    public static function isAssociatedProvider($providerCode) : bool
    {
        return in_array($providerCode, self::ASSOCIATED, false);
    }
}