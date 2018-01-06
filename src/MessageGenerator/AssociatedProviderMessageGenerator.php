<?php

namespace Refactor\MessageGenerator;


use OrderStatuses;
use PaymentTypes;
use Refactor\ReportableOrder;

class AssociatedProviderMessageGenerator implements MessageGenerator
{
    public function generate(ReportableOrder $reportableOrder)
    {
        switch ($reportableOrder->getProductStatus()) {
            case OrderStatuses::PROVIDER_PENDING:
            case OrderStatuses::PENDING:
            case OrderStatuses::WAITING_FOR_PAYMENT:
                return $this->generateMessageForPaymentMethod($reportableOrder);
            case OrderStatuses::WAITING_FOR_SHIPMENT:
                if ($reportableOrder->getPaymentMethod() == PaymentTypes::DEBIT_CARD) {
                    return ['pago confirmado pendiente de envio'];
                }

                return ['pendiente de cobro'];
            case OrderStatuses::PENDING_PROVIDER_ERROR:
            case OrderStatuses::ERROR:
                return ['pedido no confirmado por error de proveedor'];
            case OrderStatuses::CANCELLED:
            case OrderStatuses::REJECTED:
                return ['pedido cancelado o rechazado'];
        }
    }

    private function generateMessageForPaymentMethod(ReportableOrder $reportableOrder) : array
    {
        switch ($reportableOrder->getPaymentMethod()) {
            case PaymentTypes::BANK_TRANSFER:
                return ['pendiente de transferencia'];
            case PaymentTypes::PAYPAL:
            case PaymentTypes::CREDIT_CARD:
                return ['pago a crédito'];
            case PaymentTypes::DEBIT_CARD:
                return ['pago a débito'];
            default:
                if (!$reportableOrder->paymentMethodRequiresAuthorization()) {
                    return ['pago no requiere autorización'];
                }
        }
    }

}