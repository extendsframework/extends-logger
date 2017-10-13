<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
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
     */
    public function testFilter(): void
    {
        $priority = $this->createMock(PriorityInterface::class);
        $priority
            ->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn(
                2,
                3
            );

        $constraint = $this->createMock(ConstraintInterface::class);
        $constraint
            ->expects($this->once())
            ->method('validate')
            ->with(2, 3)
            ->willReturn(null);

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

        $this->assertTrue($filter->filter($log));
    }

    /**
     * Do not filter.
     *
     * Test that filter returns false when constraint returns a violation.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::filter()
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
     * Create.
     *
     * Test that create method will return an FilterInterface instance.
     *
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::create()
     * @covers \ExtendsFramework\Logger\Filter\Priority\PriorityFilter::__construct()
     */
    public function testCreate(): void
    {
        $filter = PriorityFilter::create([
            'priority' => $this->createMock(PriorityInterface::class),
            'constraint' => $this->createMock(ConstraintInterface::class),
        ]);

        $this->assertInstanceOf(FilterInterface::class, $filter);
    }
}
