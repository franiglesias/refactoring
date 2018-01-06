<?php

namespace Refactor\MessageGenerator;


use Refactor\ReportableOrder;
use Resellers;

class SoldMessageGenerator implements MessageGenerator
{
    public function generate(ReportableOrder $reportableOrder)
    {
        if ($reportableOrder->getReseller() === Resellers::RESELLER1) {
            return ['pedido confirmado con reseller 1'];
        }

        return ['pedido confirmado'];
    }
}