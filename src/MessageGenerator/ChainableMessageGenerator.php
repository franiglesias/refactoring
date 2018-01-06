<?php

namespace Refactor\MessageGenerator;


use Refactor\ReportableOrder;
use Refactor\Specification\Specification;

class ChainableMessageGenerator implements MessageGenerator
{
    private $specification;
    private $generator;
    private $next;

    public function __construct(Specification $specification, MessageGenerator $generator)
    {
        $this->specification = $specification;
        $this->generator = $generator;
    }

    public function generate(ReportableOrder $reportableOrder)
    {
        if ($this->specification->isSatisfiedBy($reportableOrder)) {
            return $this->generator->generate($reportableOrder);
        }
        if ($this->next) {
            return $this->next->generate($reportableOrder);
        }
        return [];
    }

    public function chain(ChainableMessageGenerator $delegated)
    {
        $this->next = $delegated;
        return $this->next;
    }
}