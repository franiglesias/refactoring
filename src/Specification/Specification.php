<?php

namespace Refactor\Specification;


use Refactor\ReportableOrder;

interface Specification
{
    public function isSatisfiedBy(ReportableOrder $reportableOrder) : bool;
}