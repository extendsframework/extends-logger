<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\Pdo;

use DateTime;
use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class PdoWriterTest extends TestCase
{
    /**
     * Write.
     *
     * Test that log will be written to PDO with default query string and parameters.
     *
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getStatement()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getQueryString()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getParameters()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getCallback()
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
            ->willReturn('2017-10-13 14:50:28');

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

        $statement = $this->createMock(PDOStatement::class);
        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'value' => 2,
                'keyword' => 'CRIT',
                'date_time' => '2017-10-13 14:50:28',
                'message' => 'Exceptional error!',
                'meta_data' => '{"foo":"bar"}',
            ])
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);
        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO log (value, keyword, date_time, message, meta_data) VALUES (:value, :keyword, :date_time, :message, :meta_data)')
            ->willReturn($statement);

        /**
         * @var PDO          $pdo
         * @var LogInterface $log
         */
        $writer = new PdoWriter($pdo);
        $writer->write($log);
    }

    /**
     * Custom query string.
     *
     * Test that log will be written to PDO with custom query string and parameters.
     *
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::__construct()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::write()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getStatement()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getQueryString()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getParameters()
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getCallback()
     */
    public function testCustomQueryString(): void
    {
        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn('Exceptional error!');

        $statement = $this->createMock(PDOStatement::class);
        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'message' => 'Exceptional error!',
            ])
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);
        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SQL QUERY')
            ->willReturn($statement);

        /**
         * @var PDO          $pdo
         * @var LogInterface $log
         */
        $writer = new PdoWriter($pdo, 'SQL QUERY', function (LogInterface $log) {
            return [
                'message' => $log->getMessage(),
            ];
        });
        $writer->write($log);
    }

    /**
     * Execution failed.
     *
     * Test that exception is caught when PDO in exception mode.
     *
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::__construct()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::write()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getStatement()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getQueryString()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getParameters()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getCallback()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithException::__construct()
     * @expectedException        \ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithException
     * @expectedExceptionMessage Failed to write message "Exceptional error!" to PDO. See previous exception for
     *                           details.
     */
    public function testFailedWithException(): void
    {
        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->exactly(2))
            ->method('getMessage')
            ->willReturn('Exceptional error!');

        $exception = $this->createMock(PDOException::class);

        /**
         * @var PDOException $exception
         */
        $statement = $this->createMock(PDOStatement::class);
        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'message' => 'Exceptional error!',
            ])
            ->willThrowException($exception);

        $pdo = $this->createMock(PDO::class);
        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SQL QUERY')
            ->willReturn($statement);

        /**
         * @var PDO          $pdo
         * @var LogInterface $log
         */
        $writer = new PdoWriter($pdo, 'SQL QUERY', function (LogInterface $log) {
            return [
                'message' => $log->getMessage(),
            ];
        });
        $writer->write($log);
    }

    /**
     * Execution failed.
     *
     * Test that error is triggered when PDO in silent mode.
     *
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::__construct()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::write()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getStatement()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getQueryString()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getParameters()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::getCallback()
     * @covers                   \ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithError::__construct()
     * @expectedException        \ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithError
     * @expectedExceptionMessage Failed to write message "Exceptional error!" to PDO, got error code "42S02"
     */
    public function testFailedWithError(): void
    {
        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->exactly(2))
            ->method('getMessage')
            ->willReturn('Exceptional error!');

        $statement = $this->createMock(PDOStatement::class);
        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'message' => 'Exceptional error!',
            ])
            ->willReturn(false);

        $statement
            ->expects($this->once())
            ->method('errorCode')
            ->willReturn('42S02');

        $pdo = $this->createMock(PDO::class);
        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SQL QUERY')
            ->willReturn($statement);

        /**
         * @var PDO          $pdo
         * @var LogInterface $log
         */
        $writer = new PdoWriter($pdo, 'SQL QUERY', function (LogInterface $log) {
            return [
                'message' => $log->getMessage(),
            ];
        });
        $writer->write($log);
    }

    /**
     * Factory.
     *
     * Test that factory methods returns an instance of WriterInterface.
     *
     * @covers \ExtendsFramework\Logger\Writer\Pdo\PdoWriter::factory()
     */
    public function testFactory(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->exactly(3))
            ->method('getService')
            ->withConsecutive(
                [PDO::class],
                [FilterInterface::class],
                [DecoratorInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                $this->createMock(PDO::class),
                $this->createMock(FilterInterface::class),
                $this->createMock(DecoratorInterface::class)
            );

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $writer = PdoWriter::factory(PdoWriter::class, $serviceLocator, [
            'query_string' => '',
            'callback' => function () {
            },
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
