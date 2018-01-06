<?php
namespace Refactor\MessageGenerator;


use OrderStatuses;
use Refactor\ReportableOrder;

class Provider1MessageGenerator implements MessageGenerator
{
    public function generate(ReportableOrder $reportableOrder)
    {
        $productStatus = $reportableOrder->getProductStatus();
        if ($productStatus == OrderStatuses::PENDING_PROVIDER_ERROR ||
            $productStatus == OrderStatuses::PENDING
        ) {
            return ['pedido no confirmado con provider 1'];
        }
        if ($productStatus == OrderStatuses::CANCELLED) {
            return ['pedido cancelado'];
        }

    }
}