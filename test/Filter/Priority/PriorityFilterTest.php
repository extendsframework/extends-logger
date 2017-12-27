<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Validator\Result\ResultInterface;
use ExtendsFramework\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class PriorityFilterTest extends TestCase
{
    /**
     * Filter.
     *
     * Test that filter returns true when validator is valid.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::filter()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getValidator()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getPriority()
     */
    public function testFilter(): void
    {
        $priority = $this->createMock(PriorityInterface::class);
        $priority
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(3);

        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->once())
            ->method('getPriority')
            ->willReturn($priority);

        /**
         * @var LogInterface $log
         */
        $filter = new PriorityFilter();

        $this->assertTrue($filter->filter($log));
    }

    /**
     * Do not filter.
     *
     * Test that filter returns false when validator is invalid.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::filter()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getValidator()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getPriority()
     */
    public function testDoNotFilter(): void
    {
        $priority = $this->createMock(PriorityInterface::class);
        $priority
            ->method('getValue')
            ->willReturn(3);

        $result = $this->createMock(ResultInterface::class);
        $result
            ->method('isValid')
            ->willReturn(false);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->method('validate')
            ->with(3)
            ->willReturn($result);

        $log = $this->createMock(LogInterface::class);
        $log
            ->method('getPriority')
            ->willReturn($priority);

        /**
         * @var LogInterface       $log
         * @var PriorityInterface  $priority
         * @var ValidatorInterface $validator
         */
        $filter = new PriorityFilter($priority, $validator);

        $this->assertFalse($filter->filter($log));
    }

    /**
     * Factory.
     *
     * Test that create method will return an FilterInterface instance.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::factory()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     */
    public function testFactory(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->exactly(2))
            ->method('getService')
            ->withConsecutive(
                [
                    PriorityInterface::class,
                ],
                [
                    ValidatorInterface::class,
                ]
            )
            ->willReturnOnConsecutiveCalls(
                $this->createMock(PriorityInterface::class),
                $this->createMock(ValidatorInterface::class)
            );

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $filter = PriorityFilter::factory(PriorityFilter::class, $serviceLocator, [
            'priority' => [
                'name' => PriorityInterface::class,
            ],
            'validator' => [
                'name' => ValidatorInterface::class,
            ],
        ]);

        $this->assertInstanceOf(FilterInterface::class, $filter);
    }
}
