<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 16/12/17
 * Time: 19:18
 */

namespace Refactor\Tests;

use Exception;
use Notification;
use Order;
use OrderStatuses;
use PaymentMethods;
use PaymentTypes;
use PHPUnit\Framework\TestCase;
use Providers;
use PurchaseStatus;
use Resellers;

class NotificationCharacterizationTest extends TestCase
{
    private $paymentMethodsFactory;
    private $orderFactory;

    public function setUp()
    {
        $this->paymentMethodsFactory = new PaymentMethodsStubFactory();
        $this->orderFactory = new OrderStubFactory();
        $this->orderFactory->setPaymentMethodsFactory($this->paymentMethodsFactory);
    }

    public function testMessageForEmptyProviderLocator()
    {
        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('');
        $order->method('getPaymentMethods')->willReturn(new PaymentMethods());
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(0);

        $sut = new Notification();
        $this->assertEquals(['pedido no se pudo realizar'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForProvider1CancelledOrder()
    {
        $order = $this->orderFactory->getOrderStubForProvider1();
        $order->method('getProductStatus')->willReturn(OrderStatuses::CANCELLED);
        $sut = new Notification();
        $this->assertEquals(['pedido cancelado'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForProvider1CancelledPending()
    {
        $order = $this->orderFactory->getOrderStubForProvider1();
        $order->method('getProductStatus')->willReturn(OrderStatuses::PENDING);

        $sut = new Notification();
        $this->assertEquals(['pedido no confirmado con provider 1'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForProvider1CancelledPendingBecauseProviderError()
    {
        $order = $this->orderFactory->getOrderStubForProvider1();
        $order->method('getProductStatus')->willReturn(OrderStatuses::PENDING_PROVIDER_ERROR);

        $sut = new Notification();
        $this->assertEquals(['pedido no confirmado con provider 1'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForAssociatedProviderPendingBecauseError()
    {
        $order = $this->orderFactory->getOrderStubForAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::PENDING_PROVIDER_ERROR);

        $sut = new Notification();
        $this->assertEquals(['pedido no confirmado por error de proveedor'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForAssociatedProviderError()
    {
        $order = $this->orderFactory->getOrderStubForAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::ERROR);

        $sut = new Notification();
        $this->assertEquals(['pedido no confirmado por error de proveedor'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForAssociatedProviderCancelled()
    {
        $order = $this->orderFactory->getOrderStubForAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::CANCELLED);

        $sut = new Notification();
        $this->assertEquals(['pedido cancelado o rechazado'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForAssociatedProviderRejected()
    {
        $order = $this->orderFactory->getOrderStubForAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::REJECTED);

        $sut = new Notification();
        $this->assertEquals(['pedido cancelado o rechazado'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingProvider */
    public function testMessageForAssociatedWithBankTransfer($orderStatus)
    {
        $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods(PaymentTypes::BANK_TRANSFER);
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_PAYMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        $sut = new Notification();
        $this->assertEquals(['pendiente de transferencia'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingProvider */
    public function testMessageForAssociatedWithPayPal($orderStatus)
    {
        $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods(PaymentTypes::PAYPAL);
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_PAYMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        $sut = new Notification();
        $this->assertEquals(['pago a crédito'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingProvider */
    public function testMessageForAssociatedWithCreditCard($orderStatus)
    {
        $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods(PaymentTypes::CREDIT_CARD);
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_PAYMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);
        $sut = new Notification();
        $this->assertEquals(['pago a crédito'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingProvider */
    public function testMessageForAssociatedWithDebitCard($orderStatus)
    {
        $paymentMethods = $this->paymentMethodsFactory->getDebitCard();
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_PAYMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        $sut = new Notification();
        $this->assertEquals(['pago a débito'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingProvider */
    public function testMessageForAssociatedWithNotRequiringAuthPayment($orderStatus)
    {
        $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods(PaymentTypes::AUTHORIZED_PAYMENT);
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_PAYMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        $sut = new Notification();
        $this->assertEquals(['pago no requiere autorización'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForWaitForShipmentWithDebitCard()
    {
        $paymentMethods = $this->paymentMethodsFactory->getDebitCard();
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_SHIPMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        $sut = new Notification();
        $this->assertEquals(['pago confirmado pendiente de envio'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForWaitForShipmentWithNoDebitCard()
    {
        $paymentMethods = $this->paymentMethodsFactory->getPaymentMethods(PaymentTypes::CREDIT_CARD);
        $order = $this->orderFactory->getOrderStubForAssociatedProviderNoStatus();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_SHIPMENT);
        $order->method('getPaymentMethods')->willReturn($paymentMethods);

        $sut = new Notification();
        $this->assertEquals(['pendiente de cobro'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForReservedInReseller1()
    {
        $order = $this->orderFactory->getOrderStubForAssocProviderPaymentCreditCard();
        $order->method('getStatus')->willReturn(PurchaseStatus::RESERVED);
        $order->method('getResellerCode')->willReturn(Resellers::RESELLER1);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado con reseller 1'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForSoldInReseller1()
    {
        $order = $this->orderFactory->getOrderStubForAssocProviderPaymentCreditCard();
        $order->method('getStatus')->willReturn(PurchaseStatus::SOLD);
        $order->method('getResellerCode')->willReturn(Resellers::RESELLER1);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado con reseller 1'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForReservedInOtherResellers()
    {
        $order = $this->orderFactory->getOrderStubForAssocProviderPaymentCreditCard();
        $order->method('getStatus')->willReturn(PurchaseStatus::RESERVED);
        $order->method('getResellerCode')->willReturn(Resellers::RESELLER2);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageForSoldInOtherResellers()
    {
        $order = $this->orderFactory->getOrderStubForAssocProviderPaymentCreditCard();
        $order->method('getStatus')->willReturn(PurchaseStatus::SOLD);
        $order->method('getResellerCode')->willReturn(Resellers::RESELLER2);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageNotAssociatedProviderWaitingForShipment()
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::WAITING_FOR_SHIPMENT);

        $sut = new Notification();
        $this->assertEquals(['pendiente de envio'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageNotAssociatedProviderCancelled()
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::CANCELLED);

        $sut = new Notification();
        $this->assertEquals(['pedido cancelado'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageNotAssociatedProviderPendingProviderError()
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getProductStatus')->willReturn( OrderStatuses::PENDING_PROVIDER_ERROR);

        $sut = new Notification();
        $this->assertEquals(['pendiente por error en proveedor'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider resellerProvider **/
    public function testMessageNotAssociatedProviderReservedByReseller($reseller)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getStatus')->willReturn(PurchaseStatus::RESERVED);
        $order->method('getResellerCode')->willReturn($reseller);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider resellerProvider **/
    public function testMessageNotAssociatedProviderSoldByReseller($reseller)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getStatus')->willReturn(PurchaseStatus::SOLD);
        $order->method('getResellerCode')->willReturn($reseller);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado'], $sut::getMessagesByOrderStatus($order));
    }

    public function testMessageNotAssociatedProviderReservedReseller1()
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getStatus')->willReturn(PurchaseStatus::RESERVED);
        $order->method('getResellerCode')->willReturn(Resellers::RESELLER1);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado con reseller 1'], $sut::getMessagesByOrderStatus($order));
    }
    public function testMessageNotAssociatedProviderSoldReseller1()
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider();
        $order->method('getStatus')->willReturn(PurchaseStatus::SOLD);
        $order->method('getResellerCode')->willReturn(Resellers::RESELLER1);

        $sut = new Notification();
        $this->assertEquals(['pedido confirmado con reseller 1'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingNotAssociatedProvider */
    public function testMessageNotAssociatedProviderPendingWithBankTransfer($orderStatus)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider(PaymentTypes::BANK_TRANSFER);
        $order->method('getProductStatus')->willReturn( $orderStatus);

        $sut = new Notification();
        $this->assertEquals(['pendiente de transferencia'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingNotAssociatedProvider */
    public function testMessageNotAssociatedProviderPendingWithPayPal($orderStatus)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider(PaymentTypes::PAYPAL);
        $order->method('getProductStatus')->willReturn( $orderStatus);

        $sut = new Notification();
        $this->assertEquals(['pendiente de paypal'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingNotAssociatedProvider */
    public function testMessageNotAssociatedProviderPendingWithCreditCard($orderStatus)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider(PaymentTypes::CREDIT_CARD);
        $order->method('getProductStatus')->willReturn( $orderStatus);

        $sut = new Notification();
        $this->assertEquals(['pendiente de pago con tarjeta'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingNotAssociatedProvider */
    public function testMessageNotAssociatedProviderPendingWithDebitCard($orderStatus)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider(PaymentTypes::DEBIT_CARD);
        $order->method('getProductStatus')->willReturn( $orderStatus);

        $sut = new Notification();
        $this->assertEquals(['pendiente de pago con tarjeta'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingNotAssociatedProvider */
    public function testMessageNotAssociatedProviderAuthorizedPayment($orderStatus)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProviderWithRequiredAuthorization();
        $order->method('getProductStatus')->willReturn( $orderStatus);

        $sut = new Notification();
        $this->assertEquals(['pendiente de autorización'], $sut::getMessagesByOrderStatus($order));
    }

    /** @dataProvider orderStatusPendingNotAssociatedProvider */
    public function testMessageNotAssociatedProviderNotRequiringAuthorizedPayment($orderStatus)
    {
        $order = $this->orderFactory->getOrderStubForNotAssociatedProvider(PaymentTypes::NO_REQUIRED_AUTHORIZATION_PAYMENT);
        $order->method('getProductStatus')->willReturn( $orderStatus);

        $sut = new Notification();
        $this->assertEquals(['pendiente de cobro'], $sut::getMessagesByOrderStatus($order));
    }

    public function testPaymentMethodsThrowsException()
    {
        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('locator');
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER2);
        $order->method('getPaymentMethods')->willThrowException(new Exception());

        $sut = new Notification();
        $this->assertEquals([], $sut->getMessagesByOrderStatus($order));
    }

    public function testMessageForEmptyProviderLocatorWithProvider1()
    {
        $order = $this->createMock(Order::class);
        $order->method('getProviderLocator')->willReturn('');
        $order->method('getPaymentMethods')->willReturn(new PaymentMethods());
        $order->method('getId')->willReturn('123');
        $order->method('getProvider')->willReturn(Providers::PROVIDER1);

        $sut = new Notification();
        $this->assertEquals(['pedido no se pudo realizar'], $sut::getMessagesByOrderStatus($order));
    }

    public function orderStatusPendingProvider()
    {
        return [
            'Provider pending' => [OrderStatuses::PROVIDER_PENDING],
            'Pending' => [OrderStatuses::PENDING],
            'Waiting for payment' => [OrderStatuses::WAITING_FOR_PAYMENT]
        ];
    }

    public function orderStatusPendingNotAssociatedProvider()
    {
        return [
            'Provider pending' => [OrderStatuses::PROVIDER_PENDING],
            'Pending' => [OrderStatuses::PENDING],
        ];
    }

    public function resellerProvider()
    {
        return [
            'Reseller2' => [Resellers::RESELLER2],
            'Reseller3' => [Resellers::RESELLER3],
            'Reseller4' => [Resellers::RESELLER4],
            'Reseller5' => [Resellers::RESELLER5]
        ];
    }
}
