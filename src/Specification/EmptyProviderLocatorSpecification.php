<?php

namespace Refactor\Specification;


use Refactor\ReportableOrder;

class EmptyProviderLocatorSpecification implements Specification
{

    public function __construct()
    {
    }


    public function isSatisfiedBy(ReportableOrder $reportableOrder) : bool
    {
        return empty($reportableOrder->getProviderLocator());
    }
}