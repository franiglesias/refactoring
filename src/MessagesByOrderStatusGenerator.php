<?php

namespace Refactor;

use Country;
use Logger;
use Order;
use OrderStatuses;
use PaymentMethods;
use PaymentTypes;
use Providers;
use PurchaseStatus;
use Resellers;

class MessagesByOrderStatusGenerator
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function generate(Order $order)
    {
        try {
            $paymentMethods = PaymentMethods::getFromOrder($order);

            if (empty($order->getProviderLocator())) {
                return ['pedido no se pudo realizar'];
            }

            if (null === $paymentMethods->getPaymentMethodFromOrder($order)) {
                $this->logger->debug("Medio de pago desconocido");
                return [];
            }

            if (Providers::isProvider1($order->getProvider())) {
                return $this->generateMessageForProvider1($order);
            }

            $orderStatus = $order->getStatus();

            if ($orderStatus == PurchaseStatus::RESERVED ||
                $orderStatus == PurchaseStatus::SOLD) {
                return $this->generateMessageForSoldOrder($order);
            }

            if (Providers::isAssociatedProvider($order->getProvider())) {
                $generator = new AssociatedProviderMessageGenerator();
                return $generator->generate($order, $paymentMethods);
            }

            $generator = new NoAssociatedProviderMessageGenerator();
            return $generator->generate($order, $paymentMethods);
        } catch (\Exception $e) {
        }

        return [];
    }

    private function generateMessageForProvider1(Order $order)
    {
        $productStatus = $order->getProductStatus();
        if ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR || $productStatus == OrderStatuses::PENDING
        ) {
            return ['pedido no confirmado con provider 1'];
        }
        if ($productStatus == OrderStatuses::CANCELLED) {
            return ['pedido cancelado'];
        }

        return [];
    }

    private function generateMessageForSoldOrder($order)
    {
        if ($order->getResellerCode() == Resellers::RESELLER1) {
            return ['pedido confirmado con reseller 1'];
        }

        return ['pedido confirmado'];
    }
}