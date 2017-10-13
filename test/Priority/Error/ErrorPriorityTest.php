<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Error;

use PHPUnit\Framework\TestCase;

class ErrorPriorityTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Priority\Error\ErrorPriority::getValue()
     * @covers \ExtendsFramework\Logger\Priority\Error\ErrorPriority::getKeyword()
     * @covers \ExtendsFramework\Logger\Priority\Error\ErrorPriority::getDescription()
     */
    public function testGetMethods(): void
    {
        $priority = new ErrorPriority();

        $this->assertSame(3, $priority->getValue());
        $this->assertSame('err', $priority->getKeyword());
        $this->assertSame('Error conditions.', $priority->getDescription());
    }
}
