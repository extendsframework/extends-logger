<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Emergency;

use PHPUnit\Framework\TestCase;

class EmergencyPriorityTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Priority\Emergency\EmergencyPriority::getValue()
     * @covers \ExtendsFramework\Logger\Priority\Emergency\EmergencyPriority::getKeyword()
     * @covers \ExtendsFramework\Logger\Priority\Emergency\EmergencyPriority::getDescription()
     */
    public function testGetMethods(): void
    {
        $priority = new EmergencyPriority();

        $this->assertSame(0, $priority->getValue());
        $this->assertSame('emerg', $priority->getKeyword());
        $this->assertSame('System is unusable.', $priority->getDescription());
    }
}
