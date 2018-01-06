<?php

namespace Refactor;

use Order;
use PaymentMethods;

class ReportableOrder
{
    private $order;
    private $paymentMethods;

    public function __construct(Order $order, PaymentMethods $paymentMethods)
    {
        $this->order = $order;
        $this->paymentMethods = $paymentMethods;
    }

    public function getPurchaseStatus()
    {
        return $this->order->getStatus();
    }

    public function getProductStatus()
    {
        return $this->order->getProductStatus();
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethods->getPaymentMethodFromOrder($this->order);
    }

    public function paymentMethodRequiresAuthorization()
    {
        return $this->paymentMethods->requiresAuthorization();
    }

    public function getProviderLocator()
    {
        return $this->order->getProviderLocator();
    }

    public function getProvider()
    {
        return $this->order->getProvider();
    }

    public function getReseller()
    {
        return $this->order->getResellerCode();
    }
}