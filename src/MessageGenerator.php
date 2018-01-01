<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 31/12/17
 * Time: 21:09
 */

namespace Refactor;


use Order;
use PaymentMethods;

interface MessageGenerator
{
    public function generate(Order $order, PaymentMethods $paymentMethods);
}