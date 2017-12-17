<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 17/12/17
 * Time: 13:38
 */

namespace Refactor\Tests;

use Order;
use OrderStatuses;
use PaymentMethods;
use PaymentTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Providers;

class OrderStubFactory extends TestCase
{
    private $paymentMethodsFactory;

    public function getOrderStubForAssociatedProviderNoStatus() : MockObject
    {
        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('locator');
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER3);

        return $order;
    }

    public function getOrderStubForAssocProviderPaymentCreditCard() : MockObject
    {
        $order = $this->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn(OrderStatuses::OK);
        $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods(PaymentTypes::CREDIT_CARD);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        return $order;
    }

    public function getOrderStubForProvider1() : MockObject
    {
        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('locator');
        $order->method('getPaymentMethods')->willReturn(new PaymentMethods());
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER1);

        return $order;
    }

    public function getOrderStubForAssociatedProvider() : MockObject
    {
        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('locator');
        $order->method('getPaymentMethods')->willReturn(new PaymentMethods());
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER3);

        return $order;
    }

    public function getOrderStubForNotAssociatedProvider($paymentMethod = null):MockObject
    {
        if ($paymentMethod) {
            $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods($paymentMethod);
        } else {
            $paymentMethods = new PaymentMethods();
        }

        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('locator');
        $order->method('getPaymentMethods')->willReturn($paymentMethods);
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER2);

        return $order;
    }


    public function getOrderStubForNotAssociatedProviderWithRequiredAuthorization():MockObject
    {
        $paymentMethods = $this->paymentMethodsFactory->getMethodWithRequiredAuthorization();

        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('locator');
        $order->method('getPaymentMethods')->willReturn($paymentMethods);
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER2);

        return $order;
    }


    /**
     * @param mixed $paymentMethodsFactory
     */
    public function setPaymentMethodsFactory($paymentMethodsFactory) : void
    {
        $this->paymentMethodsFactory = $paymentMethodsFactory;
    }
}