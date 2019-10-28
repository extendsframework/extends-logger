<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority;

use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class AbstractPriorityTest extends TestCase
{
    /**
     * Factory.
     *
     * Test that factory methods returns an instanceof PriorityInterface.
     *
     * @covers \ExtendsFramework\Logger\Priority\AbstractPriority::factory()
     */
    public function testFactory(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $priority = PriorityStub::factory(PriorityStub::class, $serviceLocator, []);

        $this->assertInstanceOf(PriorityInterface::class, $priority);
    }
}
