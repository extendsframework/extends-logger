<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\Stream;

use DateTime;
use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

class StreamWriterTest extends TestCase
{
    /**
     * Write.
     *
     * Test that writer will writer log message to stream.
     *
     * @covers \ExtendsFramework\Logger\Writer\Stream\StreamWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addFilter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addDecorator()
     * @covers \ExtendsFramework\Logger\Writer\Stream\StreamWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::decorate()
     * @covers \ExtendsFramework\Logger\Writer\Stream\StreamWriter::getFormattedMessage()
     */
    public function testWrite(): void
    {
        $priority = $this->createMock(PriorityInterface::class);
        $priority
            ->expects($this->once())
            ->method('getKeyword')
            ->willReturn('crit');

        $priority
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(2);

        $dateTime = $this->createMock(DateTime::class);
        $dateTime
            ->expects($this->once())
            ->method('format')
            ->willReturn('2017-10-13T14:50:28+00:00');

        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->once())
            ->method('getMetaData')
            ->willReturn(['foo' => 'bar']);

        $log
            ->expects($this->once())
            ->method('getPriority')
            ->willReturn($priority);

        $log
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn('Exceptional error!');

        $log
            ->expects($this->once())
            ->method('getDateTime')
            ->willReturn($dateTime);

        $filter = $this->createMock(FilterInterface::class);
        $filter
            ->expects($this->once())
            ->method('filter')
            ->with($log)
            ->willReturn(false);

        $decorator = $this->createMock(DecoratorInterface::class);
        $decorator
            ->expects($this->once())
            ->method('decorate')
            ->with($log)
            ->willReturnArgument(0);

        /**
         * @var LogInterface       $log
         * @var FilterInterface    $filter
         * @var DecoratorInterface $decorator
         */
        $writer = new StreamWriter('application.log');
        $result = $writer
            ->addFilter($filter)
            ->addDecorator($decorator)
            ->write($log);


        $this->assertInstanceOf(WriterInterface::class, $result);
        $this->assertSame(
            '2017-10-13T14:50:28+00:00 CRIT (2): Exceptional error! {"foo":"bar"}' . PHP_EOL,
            Buffer::get()
        );

        Buffer::reset();
    }

    /**
     * Filter.
     *
     * Test that writer will not write when log is filtered.
     *
     * @covers \ExtendsFramework\Logger\Writer\Stream\StreamWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addFilter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addDecorator()
     * @covers \ExtendsFramework\Logger\Writer\Stream\StreamWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     */
    public function testFilter(): void
    {
        $log = $this->createMock(LogInterface::class);

        $filter = $this->createMock(FilterInterface::class);
        $filter
            ->expects($this->once())
            ->method('filter')
            ->with($log)
            ->willReturn(true);

        $decorator = $this->createMock(DecoratorInterface::class);
        $decorator
            ->expects($this->never())
            ->method('decorate');

        /**
         * @var LogInterface    $log
         * @var FilterInterface $filter
         */
        $writer = new StreamWriter('application.log');
        $result = $writer
            ->addFilter($filter)
            ->write($log);

        $this->assertInstanceOf(WriterInterface::class, $result);
        $this->assertNull(Buffer::get());

        Buffer::reset();
    }

    /**
     * Write failed.
     *
     * Test that when writing to stream fails and exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Logger\Writer\Stream\StreamWriter::__construct()
     * @covers                   \ExtendsFramework\Logger\Writer\Stream\StreamWriter::write()
     * @covers                   \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     * @covers                   \ExtendsFramework\Logger\Writer\AbstractWriter::decorate()
     * @covers                   \ExtendsFramework\Logger\Writer\Stream\StreamWriter::getFormattedMessage()
     * @covers                   \ExtendsFramework\Logger\Writer\Stream\Exception\StreamWriteFailed::__construct()
     * @expectedException        \ExtendsFramework\Logger\Writer\Stream\Exception\StreamWriteFailed
     * @expectedExceptionMessage Failed to write message "2017-10-13T14:50:28+00:00 CRIT (2): Exceptional error!
     *                           {"foo":"bar"}" to stream writer.
     */
    public function testWriteFailed(): void
    {
        Buffer::set(''); // Set empty string for fwrite to return false.

        $priority = $this->createMock(PriorityInterface::class);
        $priority
            ->expects($this->once())
            ->method('getKeyword')
            ->willReturn('crit');

        $priority
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(2);

        $dateTime = $this->createMock(DateTime::class);
        $dateTime
            ->expects($this->once())
            ->method('format')
            ->willReturn('2017-10-13T14:50:28+00:00');

        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->once())
            ->method('getMetaData')
            ->willReturn(['foo' => 'bar']);

        $log
            ->expects($this->once())
            ->method('getPriority')
            ->willReturn($priority);

        $log
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn('Exceptional error!');

        $log
            ->expects($this->once())
            ->method('getDateTime')
            ->willReturn($dateTime);

        /**
         * @var LogInterface $log
         */
        $writer = new StreamWriter('application.log');
        $writer->write($log);

        Buffer::reset();
    }
}

class Buffer
{
    protected static $value;

    public static function get(): ?string
    {
        return static::$value;
    }

    public static function set(string $value): void
    {
        static::$value .= $value;
    }

    public static function reset(): void
    {
        static::$value = null;
    }
}

function fwrite(): bool
{
    if (Buffer::get() === '') {
        return false;
    }

    Buffer::set(func_get_arg(1));

    return true;
}

function fopen()
{
}

function fclose()
{
}