<?php

namespace Refactor\Tests\Specification;

use Refactor\ReportableOrder;
use Refactor\Specification\IsProvider1Specification;
use PHPUnit\Framework\TestCase;

class IsProvider1SpecificationTest extends TestCase
{
    public function testItIsNotSatisfiedByOrderWithAnotherProvider()
    {
        $orderWithProvider1 = $this->createMock(ReportableOrder::class);
        $orderWithProvider1->method('getProvider')->willReturn(\Providers::PROVIDER2);
        $sut = new IsProvider1Specification();
        $this->assertFalse($sut->isSatisfiedBy($orderWithProvider1));
    }

    public function testItIsSatisfiedByOrderWithProvider1()
    {
        $orderWithProvider1 = $this->createMock(ReportableOrder::class);
        $orderWithProvider1->method('getProvider')->willReturn(\Providers::PROVIDER1);
        $sut = new IsProvider1Specification();
        $this->assertTrue($sut->isSatisfiedBy($orderWithProvider1));
    }
}
