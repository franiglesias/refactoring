<?php

namespace Refactor\Tests;

use PaymentMethod;
use PaymentMethods;
use PaymentMethodType;
use PaymentTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaymentMethodsStubFactory extends TestCase
{
    public function getPaymentMethods($selectedMethod) : MockObject
    {
        return $this->configurePaymentMethods($selectedMethod);
    }

    public function getDebitCard() : MockObject
    {
        return $this->configurePaymentMethods(PaymentTypes::DEBIT_CARD, true);
    }

    public function getMethodWithRequiredAuthorization() : MockObject
    {
        return $this->configurePaymentMethods(PaymentTypes::REQUIRED_AUTHORIZATION_PAYMENT, false, true);
    }

    protected function configurePaymentMethods(
        string $selectedMethod,
        bool $isDebitCard = false,
        bool $requiresAuth = false) : MockObject
    {
        $paymentMethods = $this->createMock(PaymentMethods::class);
        $paymentMethods->method('getFromOrder')->willReturn($paymentMethods);
        $paymentMethods->method('hasSelectedDebitCard')->willReturn($isDebitCard);
        $paymentMethods->method('requiresAuthorization')->willReturn($requiresAuth);
        $paymentMethods->method('getPaymentMethodFromOrder')->willReturn($selectedMethod);
        return $paymentMethods;
    }
}