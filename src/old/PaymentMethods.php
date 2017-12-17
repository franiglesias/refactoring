<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 14/12/17
 * Time: 20:45
 */

class PaymentMethods
{

    public static function getFromOrder(Order $order) : PaymentMethods
    {
        return $order->getPaymentMethods();
    }

    public function getSelectedPaymentMethod()
    {
    }

    public function requiresAuthorization()
    {
    }

    public function hasSelectedDebitCard()
    {
    }

    public function hasPayuEfectivoSeleccionado()
    {
    }
}