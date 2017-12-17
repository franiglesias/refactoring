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
        $lines = [];
        $paymentMethod = null;

        try {
            $paymentMethods = PaymentMethods::getFromOrder($order);
            $selectedPaymentMethod = $paymentMethods->getSelectedPaymentMethod();
            if ($selectedPaymentMethod == null) {
                $logger = Logger::getInstance();
                $purchaseId = $order->getPurchaseId();
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
            if ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR||
                $productStatus == OrderStatuses::PENDING
            ) {
                $lines[] = 'pedido no confirmado con provider 1';
            } elseif ($productStatus == OrderStatuses::CANCELLED) {
                $lines[] = 'pedido cancelado';
            }
        } elseif (empty($providerLocator)) {
            $lines[] = 'pedido no se pudo realizar';
        } else {
            if (Providers::isAssociatedProvider($order->getProvider())) {
                $paymentMethods = PaymentMethods::getFromOrder($order);

                if ($productStatus == OrderStatuses::PROVIDER_PENDING ||
                    $productStatus == OrderStatuses::PENDING ||
                    $productStatus == OrderStatuses::WAITING_FOR_PAYMENT
                ) {
                    if ($paymentMethod == PaymentTypes::BANK_TRANSFER) {
                        $lines[] = 'pendiente de transferencia';
                    } else {
                        if ($paymentMethod == PaymentTypes::PAYPAL ||
                            $paymentMethod == PaymentTypes::CREDIT_CARD) {
                            $lines[] = 'pago a crédito';
                        } else {
                            if ($paymentMethods->hasSelectedDebitCard()) {
                                $lines[] = 'pago a débito';
                            } elseif (!$paymentMethods->requiresAuthorization()) {
                                $lines[] = 'pago no requiere autorización';
                            }
                        }
                    }
                } elseif ($productStatus == OrderStatuses::WAITING_FOR_SHIPMENT) {
                    if ($paymentMethods->hasSelectedDebitCard()) {
                        $lines[] = 'pago confirmado pendiente de envio';
                    } else {
                        $lines[] = 'pendiente de cobro';
                    }
                } elseif ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR ||
                    $productStatus == OrderStatuses::ERROR
                ) {
                    $lines[] = 'pedido no confirmado por error de proveedor';
                } elseif ($orderStatus == PurchaseStatus::RESERVED ||
                    $orderStatus == PurchaseStatus::SOLD
                ) {
                    if ($order->getResellerCode() == Resellers::RESELLER1) {
                        $lines[] = 'pedido confirmado con reseller 1';
                    } else {
                        $lines[] = 'pedido confirmado';
                    }
                } elseif ($productStatus == OrderStatuses::CANCELLED ||
                    $productStatus == OrderStatuses::REJECTED
                ) {
                    $lines[] = 'pedido cancelado o rechazado';
                }
            }
            else {
                if ($productStatus == OrderStatuses::PROVIDER_PENDING ||
                    $productStatus == OrderStatuses::PENDING) {
                    if ($paymentMethod == PaymentTypes::BANK_TRANSFER) {
                        $lines[] = 'pendiente de transferencia';
                    } elseif ($paymentMethod == PaymentTypes::PAYPAL) {
                        $lines[] = 'pendiente de paypal';
                    } elseif ($paymentMethod == PaymentTypes::CREDIT_CARD ||
                        $paymentMethod == PaymentTypes::DEBIT_CARD) {
                        $lines[] = 'pendiente de pago con tarjeta';
                    } else {
                        $paymentMethods = PaymentMethods::getFromOrder($order);
                        if ($paymentMethods->requiresAuthorization()) {
                            $lines[] = 'pendiente de autorización';
                        } else {
                            $lines[] = 'pendiente de cobro';
                        }
                    }
                } elseif ($productStatus == OrderStatuses::WAITING_FOR_SHIPMENT) {
                    $lines[] = 'pendiente de envio';
                } elseif ($productStatus == OrderStatuses::CANCELLED) {
                    $lines[] = 'pedido cancelado';
                } elseif ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR) {
                    $lines[] = 'pendiente por error en proveedor';
                } elseif ($orderStatus == PurchaseStatus::RESERVED ||
                    $orderStatus == PurchaseStatus::SOLD
                ) {
                    if ($order->getResellerCode() == Resellers::RESELLER2 ||
                        $order->getResellerCode() == Resellers::RESELLER3 ||
                        $order->getResellerCode() == Resellers::RESELLER4 ||
                        $order->getResellerCode() == Resellers::RESELLER5
                    ) {
                        $lines[] = 'pedido confirmado';
                    } else {
                        $lines[] = 'pedido confirmado reseller 1';
                    }
                }
            }
        }

        return $lines;
    }

}