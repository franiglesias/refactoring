<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 31/12/17
 * Time: 21:16
 */

namespace Refactor;


use Order;
use OrderStatuses;
use PaymentMethods;
use PaymentTypes;

class NoAssociatedProviderMessageGenerator implements MessageGenerator
{
    public function generate(Order $order, PaymentMethods $paymentMethods)
    {
        $productStatus = $order->getProductStatus();
        switch ($productStatus) {
            case OrderStatuses::PROVIDER_PENDING:
            case OrderStatuses::PENDING:
                return $this->generateMessageForPaymentMethod($order, $paymentMethods);
            case OrderStatuses::WAITING_FOR_SHIPMENT:
                return ['pendiente de envio'];
            case OrderStatuses::CANCELLED:
                return ['pedido cancelado'];
            case OrderStatuses::PENDING_PROVIDER_ERROR:
                return ['pendiente por error en proveedor'];
            default:
                return [];
        }
    }

    private function generateMessageForPaymentMethod(Order $order, PaymentMethods $paymentMethods) : array
    {
        if ($paymentMethods->requiresAuthorization()) {
            return ['pendiente de autorización'];
        }
        $paymentMethod = $paymentMethods->getPaymentMethodFromOrder($order);
        switch ($paymentMethod) {
            case PaymentTypes::BANK_TRANSFER:
                return ['pendiente de transferencia'];
            case PaymentTypes::PAYPAL:
                return ['pendiente de paypal'];
            case PaymentTypes::CREDIT_CARD:
            case PaymentTypes::DEBIT_CARD:
                return ['pendiente de pago con tarjeta'];
            default:
                return ['pendiente de cobro'];
        }
    }
}