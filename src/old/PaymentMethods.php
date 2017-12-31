<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 14/12/17
 * Time: 20:45
 */

class PaymentMethods
{

    public static function getFromOrder(Order $order)
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

    public function getPaymentMethodFromOrder(Order $order)
    {
        $selectedPaymentMethod = $this->getSelectedPaymentMethod();
        if ($selectedPaymentMethod !== null) {
            return $selectedPaymentMethod->getPaymentMethodType()->getIdTipoMedioDePago();
        }
        if ($order->getDestinationCountry() == Country::FRANCE && $order->getId() < 745) {
            return PaymentTypes::PAYPAL;
        }
        return null;
    }
}