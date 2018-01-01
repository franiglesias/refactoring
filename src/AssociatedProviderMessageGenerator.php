<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 31/12/17
 * Time: 21:11
 */

namespace Refactor;


use Order;
use OrderStatuses;
use PaymentMethods;
use PaymentTypes;

class AssociatedProviderMessageGenerator implements MessageGenerator
{
    public function generate(Order $order, PaymentMethods $paymentMethods)
    {
        $productStatus = $order->getProductStatus();

        switch ($productStatus) {
            case OrderStatuses::PROVIDER_PENDING:
            case OrderStatuses::PENDING:
            case OrderStatuses::WAITING_FOR_PAYMENT:
                return $this->generateMessageForPaymentMethod($order, $paymentMethods);
            case OrderStatuses::WAITING_FOR_SHIPMENT:
                if ($paymentMethods->hasSelectedDebitCard()) {
                    return ['pago confirmado pendiente de envio'];
                }
                return ['pendiente de cobro'];
            case OrderStatuses::PENDING_PROVIDER_ERROR:
            case OrderStatuses::ERROR:
                return ['pedido no confirmado por error de proveedor'];
            case OrderStatuses::CANCELLED:
            case OrderStatuses::REJECTED:
                return ['pedido cancelado o rechazado'];
            default:
                return [];
        }
    }

    private function generateMessageForPaymentMethod(Order $order, PaymentMethods $paymentMethods) : array
    {
        $paymentMethod = $paymentMethods->getPaymentMethodFromOrder($order);
        switch ($paymentMethod) {
            case PaymentTypes::BANK_TRANSFER:
                return ['pendiente de transferencia'];
            case PaymentTypes::PAYPAL:
            case PaymentTypes::CREDIT_CARD:
                return ['pago a crédito'];
            case PaymentTypes::DEBIT_CARD:
                return ['pago a débito'];
            default:
                if (!$paymentMethods->requiresAuthorization()) {
                    return ['pago no requiere autorización'];
                }
                return [];
        }
    }

}