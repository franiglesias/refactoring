<?php

namespace Refactor\Specification;


use PurchaseStatus;
use Refactor\ReportableOrder;

class IsSoldOrReservedOrderSpecification implements Specification
{

    public function __construct()
    {
    }

    public function isSatisfiedBy(ReportableOrder $reportableOrder) : bool
    {
        return $reportableOrder->getPurchaseStatus() === PurchaseStatus::SOLD ||
            $reportableOrder->getPurchaseStatus() === PurchaseStatus::RESERVED;
    }
}