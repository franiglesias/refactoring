<?php

namespace Refactor\MessageGenerator;


use OrderStatuses;
use PaymentTypes;
use Refactor\ReportableOrder;

class NoAssociatedProviderMessageGenerator implements MessageGenerator
{
    public function generate(ReportableOrder $reportableOrder)
    {
        switch ($reportableOrder->getProductStatus()) {
            case OrderStatuses::PROVIDER_PENDING:
            case OrderStatuses::PENDING:
                return $this->generateMessageForPaymentMethod($reportableOrder);
            case OrderStatuses::WAITING_FOR_SHIPMENT:
                return ['pendiente de envio'];
            case OrderStatuses::CANCELLED:
                return ['pedido cancelado'];
            case OrderStatuses::PENDING_PROVIDER_ERROR:
                return ['pendiente por error en proveedor'];
        }
    }

    private function generateMessageForPaymentMethod(ReportableOrder $reportableOrder) : array
    {
        if ($reportableOrder->paymentMethodRequiresAuthorization()) {
            return ['pendiente de autorización'];
        }
        switch ($reportableOrder->getPaymentMethod()) {
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