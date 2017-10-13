<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Alert;

use PHPUnit\Framework\TestCase;

class AlertPriorityTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Priority\Alert\AlertPriority::getValue()
     * @covers \ExtendsFramework\Logger\Priority\Alert\AlertPriority::getKeyword()
     * @covers \ExtendsFramework\Logger\Priority\Alert\AlertPriority::getDescription()
     */
    public function testGetMethods(): void
    {
        $priority = new AlertPriority();

        $this->assertSame(1, $priority->getValue());
        $this->assertSame('alert', $priority->getKeyword());
        $this->assertSame('Action must be taken immediately.', $priority->getDescription());
    }
}
