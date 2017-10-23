<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Validator\Constraint\ConstraintInterface;
use ExtendsFramework\Validator\Constraint\ConstraintViolationInterface;
use PHPUnit\Framework\TestCase;

class PriorityFilterTest extends TestCase
{
    /**
     * Filter.
     *
     * Test that filter returns true when constraint returns null.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::filter()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getConstraint()
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
     * Test that filter returns false when constraint returns a violation.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::filter()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getConstraint()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::getPriority()
     */
    public function testDoNotFilter(): void
    {
        $priority = $this->createMock(PriorityInterface::class);
        $priority
            ->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn(
                3,
                2
            );

        $constraint = $this->createMock(ConstraintInterface::class);
        $constraint
            ->expects($this->once())
            ->method('validate')
            ->with(3, 2)
            ->willReturn($this->createMock(ConstraintViolationInterface::class));

        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->once())
            ->method('getPriority')
            ->willReturn($priority);

        /**
         * @var LogInterface        $log
         * @var PriorityInterface   $priority
         * @var ConstraintInterface $constraint
         */
        $filter = new PriorityFilter($priority, $constraint);

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
                    PriorityInterface::class
                ],
                [
                    ConstraintInterface::class
                ]
            )
            ->willReturnOnConsecutiveCalls(
                $this->createMock(PriorityInterface::class),
                $this->createMock(ConstraintInterface::class)
            );

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $filter = PriorityFilter::factory(PriorityFilter::class, $serviceLocator, [
            'priority' => [
                'name' => PriorityInterface::class,
            ],
            'constraint' => [
                'name' => ConstraintInterface::class,
            ],
        ]);

        $this->assertInstanceOf(FilterInterface::class, $filter);
    }
}
