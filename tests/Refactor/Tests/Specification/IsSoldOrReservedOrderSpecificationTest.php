<?php

namespace Refactor\Tests;

use PHPUnit\Framework\TestCase;
use PurchaseStatus;
use Refactor\ReportableOrder;
use Refactor\Specification\IsSoldOrReservedOrderSpecification;

class IsSoldOrReservedOrderSpecificationTest extends TestCase
{
    /** @dataProvider incompletePurchasesDataProvider */
    public function testItIsNotSatisfiedByIncompleteOrder($status)
    {
        $incompleteOrder = $this->createMock(ReportableOrder::class);
        $incompleteOrder->method('getPurchaseStatus')->willReturn($status);
        $sut = new IsSoldOrReservedOrderSpecification();
        $this->assertFalse($sut->isSatisfiedBy($incompleteOrder));
    }

    public function incompletePurchasesDataProvider()
    {
        return [
            'Incomplete Purchase' => ['status' => PurchaseStatus::INCOMPLETE],
            'Errored Purchase'    => ['status' => PurchaseStatus::ERROR]
        ];
    }

    /** @dataProvider completePurchasesDataProvider */
    public function testItIsSatisfiedByCompletedOrder($status)
    {
        $sut = new IsSoldOrReservedOrderSpecification();
        $completedOrder = $this->createMock(ReportableOrder::class);
        $completedOrder->method('getPurchaseStatus')->willReturn($status);
        $this->assertTrue($sut->isSatisfiedBy($completedOrder));
    }

    public function completePurchasesDataProvider()
    {
        return [
            'Reserved Purchase' => ['status' => PurchaseStatus::RESERVED],
            'Sold Purchase'     => ['status' => PurchaseStatus::SOLD]
        ];
    }
}
