<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Debug;

use PHPUnit\Framework\TestCase;

class DebugPriorityTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Priority\Debug\DebugPriority::getValue()
     * @covers \ExtendsFramework\Logger\Priority\Debug\DebugPriority::getKeyword()
     * @covers \ExtendsFramework\Logger\Priority\Debug\DebugPriority::getDescription()
     */
    public function testGetMethods(): void
    {
        $priority = new DebugPriority();

        $this->assertSame(7, $priority->getValue());
        $this->assertSame('debug', $priority->getKeyword());
        $this->assertSame('Debug-level messages.', $priority->getDescription());
    }
}
