<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 5/1/18
 * Time: 20:15
 */

namespace Refactor\Tests;

use Order;
use PaymentTypes;
use Refactor\ReportableOrder;
use PHPUnit\Framework\TestCase;

class ReportableOrderTest extends TestCase
{
    public function setUp()
    {
        $order = $this->createMock(Order::class);
        $order->method('getStatus')->willReturn('order status');
        $order->method('getProductStatus')->willReturn('product status');
        $paymentMethods = $this->getPaymentMethodsWithMethod('a payment method');
        $this->sut = new ReportableOrder($order, $paymentMethods);
    }

    public function testItCanGetPurchaseStatus()
    {
        $this->assertEquals('order status', $this->sut->getPurchaseStatus());
    }

    public function testItCanGetProductStatus()
    {
        $this->assertEquals('product status', $this->sut->getProductStatus());
    }


    public function testItCanGetPaymentMethod()
    {
       $this->assertEquals('a payment method', $this->sut->getPaymentMethod());
    }

    public function testItCanGetPaymentMethodRequiersAuthorization()
    {
        $this->assertTrue($this->sut->paymentMethodRequiresAuthorization());
    }


    public function getPaymentMethodsWithMethod($method)
    {
        $paymentMethodsFactory = new PaymentMethodsStubFactory();
        return $paymentMethodsFactory->getPaymentMethods($method);
    }

}
