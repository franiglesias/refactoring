<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 6/1/18
 * Time: 9:43
 */

namespace Refactor\Tests\Specification;

use Providers;
use Refactor\ReportableOrder;
use Refactor\Specification\NotAssociatedProviderSpecification;
use PHPUnit\Framework\TestCase;

class NotAssociatedProviderSpecificationTest extends TestCase
{
    /** @dataProvider AssociatedProvidersDataProvider */
    public function testItIsNotSatisfiedByAssociatedProviders($provider)
    {
        $sut = new NotAssociatedProviderSpecification();
        $orderForAssociatedProvider = $this->createMock(ReportableOrder::class);
        $orderForAssociatedProvider->method('getProvider')->willReturn($provider);
        $this->assertFalse($sut->isSatisfiedBy($orderForAssociatedProvider));
    }

    public function AssociatedProvidersDataProvider()
    {
        return [
            'Provider 3' => ['provider' => Providers::PROVIDER3],
            'Provider 4' => ['provider' => Providers::PROVIDER4]
        ];
    }

    /** @dataProvider NotAssociatedProvidersDataProvider */
    public function testItIsSatisfiedByNotAssociatedProviders($provider)
    {
        $sut = new NotAssociatedProviderSpecification();
        $orderForNotAssociatedProvider = $this->createMock(ReportableOrder::class);
        $orderForNotAssociatedProvider->method('getProvider')->willReturn($provider);
        $this->assertTrue($sut->isSatisfiedBy($orderForNotAssociatedProvider));
    }

    public function NotAssociatedProvidersDataProvider()
    {
        return [
            'Provider 1' => ['provider' => Providers::PROVIDER1],
            'Provider 2' => ['provider' => Providers::PROVIDER2],
            'Provider 5' => ['provider' => Providers::PROVIDER5],
            'Provider 6' => ['provider' => Providers::PROVIDER6]
        ];
    }
}
