<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Notice;

use PHPUnit\Framework\TestCase;

class NoticePriorityTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Priority\Notice\NoticePriority::getValue()
     * @covers \ExtendsFramework\Logger\Priority\Notice\NoticePriority::getKeyword()
     * @covers \ExtendsFramework\Logger\Priority\Notice\NoticePriority::getDescription()
     */
    public function testGetMethods(): void
    {
        $priority = new NoticePriority();

        $this->assertSame(5, $priority->getValue());
        $this->assertSame('notice', $priority->getKeyword());
        $this->assertSame('Normal but significant conditions.', $priority->getDescription());
    }
}
