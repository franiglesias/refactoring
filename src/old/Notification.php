<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 14/12/17
 * Time: 20:34
 */

class Notification
{
    /**
     * @return array
     */
    public static function getMessagesByOrderStatus(Order $order)
    {
        $productStatus = (int) $order->getProductStatus();
        $orderStatus = (int) $order->getStatus();
        $providerLocator = $order->getProviderLocator();
        $paymentMethod = null;

        try {
            $paymentMethods = PaymentMethods::getFromOrder($order);
            $selectedPaymentMethod = $paymentMethods->getSelectedPaymentMethod();
            if ($selectedPaymentMethod == null) {
                $logger = Logger::getInstance();
                $orderId = $order->getId();
                $logger->debug("Medio de pago desconocido");
                if ($order->getDestinationCountry() == Country::FRANCE && $orderId < 745) {
                    $paymentMethod = PaymentTypes::PAYPAL;
                }
            } else {
                $paymentMethod = $selectedPaymentMethod->getPaymentMethodType()->getIdTipoMedioDePago();
            }
        } catch (Exception $e) {
        }

        if (Providers::isProvider1($order->getProvider())) {
            if ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR || $productStatus == OrderStatuses::PENDING
            ) {
                return ['pedido no confirmado con provider 1'];
            }
            if ($productStatus == OrderStatuses::CANCELLED) {
                return ['pedido cancelado'];
            }
        }

        if (empty($providerLocator)) {
            return ['pedido no se pudo realizar'];
        }

        if (Providers::isAssociatedProvider($order->getProvider())) {

            if ($productStatus == OrderStatuses::PROVIDER_PENDING ||
                $productStatus == OrderStatuses::PENDING ||
                $productStatus == OrderStatuses::WAITING_FOR_PAYMENT
            ) {
                if ($paymentMethod == PaymentTypes::BANK_TRANSFER) {
                    return ['pendiente de transferencia'];
                }
                if ($paymentMethod == PaymentTypes::PAYPAL || $paymentMethod == PaymentTypes::CREDIT_CARD) {
                    return ['pago a crédito'];
                }
                if ($paymentMethods->hasSelectedDebitCard()) {
                    return ['pago a débito'];
                }
                if (!$paymentMethods->requiresAuthorization()) {
                    return ['pago no requiere autorización'];
                }
            }

            if ($productStatus == OrderStatuses::WAITING_FOR_SHIPMENT) {
                if ($paymentMethods->hasSelectedDebitCard()) {
                    return ['pago confirmado pendiente de envio'];
                }

                return ['pendiente de cobro'];
            }

            if ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR || $productStatus == OrderStatuses::ERROR
            ) {
                return ['pedido no confirmado por error de proveedor'];
            }

            if ($orderStatus == PurchaseStatus::RESERVED || $orderStatus == PurchaseStatus::SOLD) {
                if ($order->getResellerCode() == Resellers::RESELLER1) {
                    return ['pedido confirmado con reseller 1'];
                }

                return ['pedido confirmado'];
            }

            if ($productStatus == OrderStatuses::CANCELLED || $productStatus == OrderStatuses::REJECTED
            ) {
                return ['pedido cancelado o rechazado'];
            }
        }
        if ($productStatus == OrderStatuses::PROVIDER_PENDING || $productStatus == OrderStatuses::PENDING) {
            if ($paymentMethod == PaymentTypes::BANK_TRANSFER) {
                return ['pendiente de transferencia'];
            }

            if ($paymentMethod == PaymentTypes::PAYPAL) {
                return ['pendiente de paypal'];
            }

            if ($paymentMethod == PaymentTypes::CREDIT_CARD || $paymentMethod == PaymentTypes::DEBIT_CARD) {
                return ['pendiente de pago con tarjeta'];
            }
            if ($paymentMethods->requiresAuthorization()) {
                return ['pendiente de autorización'];
            }

            return ['pendiente de cobro'];
        }
        if ($productStatus == OrderStatuses::WAITING_FOR_SHIPMENT) {
            return ['pendiente de envio'];
        }

        if ($productStatus == OrderStatuses::CANCELLED) {
            return ['pedido cancelado'];
        }

        if ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR) {
            return ['pendiente por error en proveedor'];
        }

        if ($orderStatus == PurchaseStatus::RESERVED || $orderStatus == PurchaseStatus::SOLD
        ) {
            if ($order->getResellerCode() == Resellers::RESELLER2 ||
                $order->getResellerCode() == Resellers::RESELLER3 ||
                $order->getResellerCode() == Resellers::RESELLER4 ||
                $order->getResellerCode() == Resellers::RESELLER5
            ) {
                return ['pedido confirmado'];
            }

            return ['pedido confirmado reseller 1'];
        }

        return [];
    }
}