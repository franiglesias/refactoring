<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 6/1/18
 * Time: 16:48
 */

namespace Refactor\Tests\MessageGenerator;

use Refactor\MessageGenerator\ChainableMessageGenerator;
use PHPUnit\Framework\TestCase;
use Refactor\MessageGenerator\MessageGenerator;
use Refactor\ReportableOrder;
use Refactor\Specification\Specification;

class ChainableMessageGeneratorTest extends TestCase
{
    public function testItReturnsEmptyMessageIfSpecificationIsNotSatisfied()
    {
        $specification = $this->createMock(Specification::class);
        $specification->method('isSatisfiedBy')->willReturn(false);

        $generator = $this->createMock(MessageGenerator::class);
        $reportableOrder = $this->createMock(ReportableOrder::class);

        $sut = new ChainableMessageGenerator($specification, $generator);
        $this->assertEquals([], $sut->generate($reportableOrder));
    }

    public function testItReturnsGeneratedMessageIfSpecificationIsSatisfied()
    {
        $specification = $this->createMock(Specification::class);
        $specification->method('isSatisfiedBy')->willReturn(true);

        $generator = $this->createMock(MessageGenerator::class);
        $generator->method('generate')->willReturn(['something']);

        $reportableOrder = $this->createMock(ReportableOrder::class);

        $sut = new ChainableMessageGenerator($specification, $generator);
        $this->assertEquals(['something'], $sut->generate($reportableOrder));
    }

    public function testItDelegatesIfThereIsAChainedGeneratorAndSpecificationIsNotSatisfied()
    {
        $specification = $this->createMock(Specification::class);
        $specification->method('isSatisfiedBy')->willReturn(false);

        $generator = $this->createMock(MessageGenerator::class);
        $generator->method('generate')->willReturn(['something']);

        $delegated = $this->createMock(ChainableMessageGenerator::class);
        $delegated->method('generate')->willReturn(['delegated']);

        $reportableOrder = $this->createMock(ReportableOrder::class);

        $sut = new ChainableMessageGenerator($specification, $generator);
        $sut->chain($delegated);
        $this->assertEquals(['delegated'], $sut->generate($reportableOrder));
    }

    public function testItCanChainGeneratorsInRightOrder()
    {
        $specification = $this->createMock(Specification::class);
        $specification->method('isSatisfiedBy')->willReturn(false);

        $generator = $this->createMock(MessageGenerator::class);
        $generator->method('generate')->willReturn(['something']);

        $delegated = $this->createMock(ChainableMessageGenerator::class);
        $delegated->method('generate')->willReturn(['delegated']);

        $sut = new ChainableMessageGenerator($specification, $generator);
        $this->assertEquals($delegated, $sut->chain($delegated));
    }

}
