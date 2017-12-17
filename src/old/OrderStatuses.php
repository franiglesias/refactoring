<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 14/12/17
 * Time: 20:47
 */

class OrderStatuses
{
    public const PROVIDER_ERROR = 1;
    public const PENDING = 2;
    public const CANCELLED = 3;
    public const PROVIDER_PENDING = 4;
    public const WAITING_FOR_PAYMENT = 5;
    public const WAITING_FOR_SHIPMENT = 6;
    public const ERROR = 7;
    public const REJECTED = 8;
    public const PENDING_PROVIDER_ERROR = 9;
    public const OK = 10;
}