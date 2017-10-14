<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\Stream\Exception\StreamWriteFailed;
use ExtendsFramework\Logger\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    /**
     * Log.
     *
     * Test that message will be logged with priority and meta data.
     *
     * @covers \ExtendsFramework\Logger\Logger::addWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::__construct()
     * @covers \ExtendsFramework\Logger\LoggerWriter::getWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::mustInterrupt()
     * @covers \ExtendsFramework\Logger\Logger::log()
     * @covers \ExtendsFramework\Logger\Logger::getLog()
     */
    public function testLog(): void
    {
        $priority = $this->createMock(PriorityInterface::class);

        $writer = $this->createMock(WriterInterface::class);
        $writer
            ->expects($this->once())
            ->method('write')
            ->with($this->callback(function (LogInterface $log) use ($priority) {
                $this->assertSame('Error!', $log->getMessage());
                $this->assertSame($priority, $log->getPriority());
                $this->assertSame(['foo' => 'bar'], $log->getMetaData());

                return $this;
            }));

        /**
         * @var WriterInterface   $writer
         * @var PriorityInterface $priority
         */
        $logger = new Logger();
        $logger
            ->addWriter($writer)
            ->log('Error!', $priority, ['foo' => 'bar']);
    }

    /**
     * Syslog.
     *
     * Test that logger will write to syslog when writer throws an exception will writing.
     *
     * @covers \ExtendsFramework\Logger\Logger::addWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::__construct()
     * @covers \ExtendsFramework\Logger\LoggerWriter::getWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::mustInterrupt()
     * @covers \ExtendsFramework\Logger\Logger::log()
     * @covers \ExtendsFramework\Logger\Logger::getLog()
     */
    public function testSyslog(): void
    {
        $exception = $this->createMock(StreamWriteFailed::class);

        /**
         * @var StreamWriteFailed $exception
         */
        $writer = $this->createMock(WriterInterface::class);
        $writer
            ->expects($this->once())
            ->method('write')
            ->willThrowException($exception);

        /**
         * @var WriterInterface    $writer
         * @var DecoratorInterface $decorator
         * @var PriorityInterface  $priority
         */
        $logger = new Logger();
        $logger
            ->addWriter($writer)
            ->log('Error!');

        $this->assertSame(2, Buffer::getPriority());
        $this->assertSame('', Buffer::getMessage()); // Can not mock getMessage() return value.

        Buffer::reset();
    }

    /**
     * Interrupt.
     *
     * Test that writer will interrupt next writers.
     *
     * @covers \ExtendsFramework\Logger\Logger::addWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::__construct()
     * @covers \ExtendsFramework\Logger\LoggerWriter::getWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::mustInterrupt()
     * @covers \ExtendsFramework\Logger\Logger::log()
     * @covers \ExtendsFramework\Logger\Logger::getLog()
     */
    public function testInterrupt(): void
    {
        $priority = $this->createMock(PriorityInterface::class);

        $writer = $this->createMock(WriterInterface::class);
        $writer
            ->expects($this->once())
            ->method('write');

        /**
         * @var WriterInterface   $writer
         * @var PriorityInterface $priority
         */
        $logger = new Logger();
        $logger
            ->addWriter($writer, true)
            ->addWriter($writer)
            ->addWriter($writer)
            ->log('Error!', $priority, ['foo' => 'bar']);
    }
}

class Buffer
{
    protected static $priority;

    protected static $message;

    public static function getMessage(): string
    {
        return static::$message;
    }

    public static function getPriority(): int
    {
        return static::$priority;
    }

    public static function set(int $priority, string $message): void
    {
        static::$priority = $priority;
        static::$message = $message;
    }

    public static function reset(): void
    {
        static::$priority = null;
        static::$message = null;
    }
}

function syslog(int $priority, string $message): bool
{
    Buffer::set($priority, $message);

    return true;
}
