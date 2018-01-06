<?php

namespace Refactor\Tests\Specification;

use Refactor\ReportableOrder;
use Refactor\Specification\EmptyProviderLocatorSpecification;
use PHPUnit\Framework\TestCase;

class EmptyProviderLocatorSpecificationTest extends TestCase
{
    public function testItIsNotSatisfiedByAnOrderWithLocator()
    {
        $sut = new EmptyProviderLocatorSpecification();
        $orderWithLocator = $this->createMock(ReportableOrder::class);
        $orderWithLocator->method('getProviderLocator')->willReturn('locator');
        $this->assertFalse($sut->isSatisfiedBy($orderWithLocator));
    }

    public function testItIsSatisfiedByOrderWithoutLocator()
    {
        $sut = new EmptyProviderLocatorSpecification();
        $orderWithoutLocator = $this->createMock(ReportableOrder::class);
        $orderWithoutLocator->method('getProviderLocator')->willReturn('');
        $this->assertTrue($sut->isSatisfiedBy($orderWithoutLocator));
    }
}
