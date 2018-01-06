<?php

namespace Refactor\Specification;


use Providers;
use Refactor\ReportableOrder;

class NotAssociatedProviderSpecification implements Specification
{

    /**
     * NotAssociatedProviderSpecification constructor.
     */
    public function __construct()
    {
    }

    public function isSatisfiedBy(ReportableOrder $reportableOrder) : bool
    {
        return !Providers::isAssociatedProvider($reportableOrder->getProvider());
    }
}