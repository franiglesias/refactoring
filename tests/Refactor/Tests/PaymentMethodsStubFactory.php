<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 17/12/17
 * Time: 12:49
 */

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
        return $this->configurePaymentMethods(PaymentTypes::AUTHORIZED_PAYMENT, false, true);
    }

    protected function configurePaymentMethods(
        string $selectedMethod,
        bool $isDebitCard = false,
        bool $requiresAuth = false) : MockObject
    {
        $paymentMethodType = $this->createMock(PaymentMethodType::class);
        $paymentMethodType->method('getIdTipoMedioDePago')->willReturn($selectedMethod);

        $paymentMethod = $this->createMock(PaymentMethod::class);
        $paymentMethod->method('getPaymentMethodType')->willReturn($paymentMethodType);

        $paymentMethods = $this->createMock(PaymentMethods::class);
        $paymentMethods->method('getFromOrder')->willReturn($paymentMethods);
        $paymentMethods->method('getSelectedPaymentMethod')->willReturn($paymentMethod);
        $paymentMethods->method('hasSelectedDebitCard')->willReturn($isDebitCard);
        $paymentMethods->method('requiresAuthorization')->willReturn($requiresAuth);

        return $paymentMethods;
    }
}