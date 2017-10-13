<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Informational;

use PHPUnit\Framework\TestCase;

class InformationalPriorityTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Priority\Informational\InformationalPriority::getValue()
     * @covers \ExtendsFramework\Logger\Priority\Informational\InformationalPriority::getKeyword()
     * @covers \ExtendsFramework\Logger\Priority\Informational\InformationalPriority::getDescription()
     */
    public function testGetMethods(): void
    {
        $priority = new InformationalPriority();

        $this->assertSame(6, $priority->getValue());
        $this->assertSame('info', $priority->getKeyword());
        $this->assertSame('Informational messages.', $priority->getDescription());
    }
}
