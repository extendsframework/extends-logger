<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\File;

use DateTime;
use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    /**
     * Write.
     *
     * Test that writer will writer log message to file.
     *
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addFilter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addDecorator()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::getFilters()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::getDecorators())
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::decorate()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFormattedMessage()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getLogFormat()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFilename()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getLocation()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFileFormat()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getNewLine()
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
        $writer = new FileWriter('/var/log/extends');
        $result = $writer
            ->addFilter($filter)
            ->addDecorator($decorator)
            ->write($log);

        $this->assertInstanceOf(WriterInterface::class, $result);
        $this->assertSame('/var/log/extends/' . date('Y-m-d') . '.log', Buffer::getFilename());
        $this->assertSame(
            '2017-10-13T14:50:28+00:00 CRIT (2): Exceptional error! {"foo":"bar"}' . PHP_EOL,
            Buffer::getValue()
        );

        Buffer::reset();
    }

    /**
     * Filter.
     *
     * Test that custom format and new line character are used for write.
     *
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addFilter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addDecorator()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::getFilters()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::getDecorators())
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getLogFormat()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFilename()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getLocation()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFileFormat()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getNewLine()
     */
    public function testCustomFormat(): void
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
        $writer = new FileWriter('/var/log/extends', 'd-m-Y', '{keyword} ({value}): {message} {metaData}, {datetime}',
            "\n\r");
        $result = $writer
            ->addFilter($filter)
            ->addDecorator($decorator)
            ->write($log);

        $this->assertInstanceOf(WriterInterface::class, $result);
        $this->assertSame('/var/log/extends/' . date('d-m-Y') . '.log', Buffer::getFilename());
        $this->assertSame(
            'CRIT (2): Exceptional error! {"foo":"bar"}, 2017-10-13T14:50:28+00:00' . "\n\r",
            Buffer::getValue()
        );

        Buffer::reset();
    }

    /**
     * Filter.
     *
     * Test that writer will not write when log is filtered.
     *
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addFilter()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::addDecorator()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::getFilters()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::getDecorators())
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getLogFormat()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFilename()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getLocation()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getFileFormat()
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::getNewLine()
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
        $writer = new FileWriter('/var/log/extends');
        $result = $writer
            ->addFilter($filter)
            ->write($log);

        $this->assertInstanceOf(WriterInterface::class, $result);
        $this->assertNull(Buffer::getFilename());
        $this->assertNull(Buffer::getValue());

        Buffer::reset();
    }

    /**
     * Write failed.
     *
     * Test that when writing to file fails and exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Logger\Writer\File\FileWriter::__construct()
     * @covers                   \ExtendsFramework\Logger\Writer\File\FileWriter::write()
     * @covers                   \ExtendsFramework\Logger\Writer\AbstractWriter::filter()
     * @covers                   \ExtendsFramework\Logger\Writer\AbstractWriter::decorate()
     * @covers                   \ExtendsFramework\Logger\Writer\AbstractWriter::getFilters()
     * @covers                   \ExtendsFramework\Logger\Writer\AbstractWriter::getDecorators())
     * @covers                   \ExtendsFramework\Logger\Writer\File\FileWriter::getFormattedMessage()
     * @covers                   \ExtendsFramework\Logger\Writer\File\Exception\FileWriterFailed::__construct()
     * @expectedException        \ExtendsFramework\Logger\Writer\File\Exception\FileWriterFailed
     * @expectedExceptionMessage Failed to write message "2017-10-13T14:50:28+00:00 CRIT (2): Exceptional error!
     *                           {"foo":"bar"}" to file "application.log".
     */
    public function testWriteFailed(): void
    {
        Buffer::setValue(''); // Set empty string for fwrite to return false.

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
        $writer = new FileWriter('/var/log/extends');
        $writer->write($log);

        Buffer::reset();
    }

    /**
     * Factory.
     *
     * Test that factory methods returns an instance of WriterInterface.
     *
     * @covers \ExtendsFramework\Logger\Writer\File\FileWriter::factory()
     */
    public function testFactory(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->exactly(2))
            ->method('getService')
            ->withConsecutive(
                [FilterInterface::class],
                [DecoratorInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                $this->createMock(FilterInterface::class),
                $this->createMock(DecoratorInterface::class)
            );

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $writer = FileWriter::factory(FileWriter::class, $serviceLocator, [
            'location' => '/var/log/extends',
            'file_format' => '',
            'log_format' => '',
            'new_line' => '',
            'filters' => [
                [
                    'name' => FilterInterface::class,
                ],
            ],
            'decorators' => [
                [
                    'name' => DecoratorInterface::class,
                ],
            ],
        ]);

        $this->assertInstanceOf(WriterInterface::class, $writer);
    }
}

class Buffer
{
    protected static $filename;

    protected static $value;

    public static function getFilename(): ?string
    {
        return self::$filename;
    }

    public static function setFilename($filename): void
    {
        self::$filename = $filename;
    }

    public static function getValue(): ?string
    {
        return self::$value;
    }

    public static function setValue(string $value): void
    {
        static::$value .= $value;
    }

    public static function reset(): void
    {
        static::$value = null;
        static::$filename = null;
    }
}

function fwrite(): bool
{
    if (Buffer::getValue() === '') {
        return false;
    }

    Buffer::setValue(func_get_arg(1));

    return true;
}

function fopen()
{
    Buffer::setFilename(func_get_arg(0));
}

function fclose()
{
}
