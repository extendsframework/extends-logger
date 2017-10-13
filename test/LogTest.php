<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use DateTime;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\Log::__construct()
     * @covers \ExtendsFramework\Logger\Log::getMessage()
     * @covers \ExtendsFramework\Logger\Log::getPriority()
     * @covers \ExtendsFramework\Logger\Log::getDateTime()
     * @covers \ExtendsFramework\Logger\Log::getMetaData()
     */
    public function testGetMethods(): void
    {
        $priority = $this->createMock(PriorityInterface::class);

        $dateTime = $this->createMock(DateTime::class);

        /**
         * @var PriorityInterface $priority
         * @var DateTime          $dateTime
         */
        $log = new Log('Error!', $priority, $dateTime, [
            'foo' => 'bar',
        ]);

        $this->assertSame('Error!', $log->getMessage());
        $this->assertSame($priority, $log->getPriority());
        $this->assertSame($dateTime, $log->getDateTime());
        $this->assertSame(['foo' => 'bar'], $log->getMetaData());
    }

    /**
     * With methods.
     *
     * Test that methods will change the message, meta data and return a new instance.
     *
     * @covers \ExtendsFramework\Logger\Log::__construct()
     * @covers \ExtendsFramework\Logger\Log::withMessage()
     * @covers \ExtendsFramework\Logger\Log::withMetaData()
     * @covers \ExtendsFramework\Logger\Log::getMessage()
     * @covers \ExtendsFramework\Logger\Log::getMetaData()
     */
    public function testWithMethods(): void
    {
        $log = new Log('Error!', null, null, [
            'foo' => 'bar',
        ]);
        $instance = $log
            ->withMessage('Exception!')
            ->withMetaData([
                'bar' => 'baz',
            ]);

        $this->assertNotSame($log, $instance);
        $this->assertInstanceOf(LogInterface::class, $instance);
        $this->assertSame('Exception!', $instance->getMessage());
        $this->assertSame(['bar' => 'baz'], $instance->getMetaData());
    }

    /**
     * And methods.
     *
     * Test that methods will change the message, meta data and return a new instance.
     *
     * @covers \ExtendsFramework\Logger\Log::__construct()
     * @covers \ExtendsFramework\Logger\Log::andMetaData()
     * @covers \ExtendsFramework\Logger\Log::getMetaData()
     */
    public function testAndMethods(): void
    {
        $log = new Log('Error!', null, null, [
            'foo' => 'bar',
        ]);
        $instance = $log->andMetaData('bar', 'baz');

        $this->assertNotSame($log, $instance);
        $this->assertInstanceOf(LogInterface::class, $instance);
        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $instance->getMetaData());
    }
}
