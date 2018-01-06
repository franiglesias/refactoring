<?php
namespace Refactor\Specification;


use Providers;
use Refactor\ReportableOrder;

class IsProvider1Specification implements Specification
{

    public function __construct()
    {
    }

    public function isSatisfiedBy(ReportableOrder $reportableOrder) : bool
    {
        return Providers::isProvider1($reportableOrder->getProvider());
    }
}