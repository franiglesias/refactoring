<?php

namespace Refactor\Tests\MessageGenerator;

use Refactor\MessageGenerator\NoLocatorMessageGenerator;
use PHPUnit\Framework\TestCase;
use Refactor\ReportableOrder;

class NoLocatorMessageGeneratorTest extends TestCase
{
    public function testItGeneratesTheMessage()
    {

        $sut = new NoLocatorMessageGenerator();
        $reportableOrder = $this->createMock(ReportableOrder::class);
        $this->assertEquals(['pedido no se pudo realizar'], $sut->generate($reportableOrder));

    }
}
