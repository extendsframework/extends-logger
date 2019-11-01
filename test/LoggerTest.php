<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\File\Exception\FileWriterFailed;
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

                return true;
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
     */
    public function testSyslog(): void
    {
        $exception = $this->createMock(FileWriterFailed::class);

        /**
         * @var FileWriterFailed $exception
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

/**
 * @param int    $priority
 * @param string $message
 * @return bool
 */
function syslog(int $priority, string $message): bool
{
    Buffer::set($priority, $message);

    return true;
}
