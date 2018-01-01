<?php

namespace Refactor\Tests;

use Order;
use PaymentMethods;
use Providers;
use Refactor\MessagesByOrderStatusGenerator;
use PHPUnit\Framework\TestCase;

class MessagesByOrderStatusGeneratorTest extends TestCase
{

    public function testLogsUnknownPaymentMethod()
    {
        $logger = $this->createMock(\Logger::class);
        $logger->expects($this->once())->method('debug')->with('Medio de pago desconocido');

        $paymentMethods = $this->createMock(PaymentMethods::class);
        $paymentMethods->method('getFromOrder')->willReturn($paymentMethods);
        $paymentMethods->method('getPaymentMethodFromOrder')->willReturn(null);

        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('123');
        $order->method('getPaymentMethods')->willReturn($paymentMethods);
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER2);

        $sut = new MessagesByOrderStatusGenerator($logger);
        $this->assertEquals([], $sut->generate($order));
    }
}
