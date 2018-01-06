<?php

namespace Refactor\MessageGenerator;


use Refactor\ReportableOrder;

interface MessageGenerator
{
    public function generate(ReportableOrder $reportableOrder);
}