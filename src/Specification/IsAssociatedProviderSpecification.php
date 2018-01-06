<?php

namespace Refactor\Specification;


use Providers;
use Refactor\ReportableOrder;

class IsAssociatedProviderSpecification implements Specification
{
    public function __construct()
    {
    }

    public function isSatisfiedBy(ReportableOrder $reportableOrder) : bool
    {
        return Providers::isAssociatedProvider($reportableOrder->getProvider());
    }
}