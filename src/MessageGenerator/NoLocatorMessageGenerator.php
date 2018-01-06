<?php

namespace Refactor\MessageGenerator;


use Refactor\ReportableOrder;

class NoLocatorMessageGenerator implements MessageGenerator
{

    /**
     * NoLocatorMessageGenerator constructor.
     */
    public function __construct()
    {
    }

    public function generate(ReportableOrder $reportableOrder)
    {
        return ['pedido no se pudo realizar'];
    }
}