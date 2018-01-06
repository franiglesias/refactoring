<?php

use Refactor\MessageGenerator\AssociatedProviderMessageGenerator;
use Refactor\MessageGenerator\ChainableMessageGenerator;
use Refactor\MessageGenerator\NoAssociatedProviderMessageGenerator;
use Refactor\MessageGenerator\NoLocatorMessageGenerator;
use Refactor\MessageGenerator\Provider1MessageGenerator;
use Refactor\MessageGenerator\SoldMessageGenerator;
use Refactor\ReportableOrder;
use Refactor\Specification\EmptyProviderLocatorSpecification;
use Refactor\Specification\IsAssociatedProviderSpecification;
use Refactor\Specification\IsProvider1Specification;
use Refactor\Specification\IsSoldOrReservedOrderSpecification;
use Refactor\Specification\NotAssociatedProviderSpecification;

class Notification
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public static function getMessagesByOrderStatus(Order $order)
    {
        try {
            $logger = Logger::getInstance();
            $paymentMethods = PaymentMethods::getFromOrder($order);
            $reportableOrder = new ReportableOrder($order, $paymentMethods);

            if (null === $reportableOrder->getPaymentMethod()) {
                $logger->debug("Medio de pago desconocido");

                return [];
            }

            $generatorChain = new ChainableMessageGenerator(
                new EmptyProviderLocatorSpecification(),
                new NoLocatorMessageGenerator()
            );
            $generatorChain
                ->chain(new ChainableMessageGenerator(
                    new IsProvider1Specification(),
                    new Provider1MessageGenerator()
                ))
                ->chain(new ChainableMessageGenerator(
                    new IsSoldOrReservedOrderSpecification(),
                    new SoldMessageGenerator()
                ))
                ->chain(new ChainableMessageGenerator(
                    new IsAssociatedProviderSpecification(),
                    new AssociatedProviderMessageGenerator()
                ))
                ->chain(new ChainableMessageGenerator(
                    new NotAssociatedProviderSpecification(),
                    new NoAssociatedProviderMessageGenerator()
                ));

            return $generatorChain->generate($reportableOrder);
        } catch (Exception $e) {
            return [];
        }
    }
}