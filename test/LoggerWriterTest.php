<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use ExtendsFramework\Logger\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

class LoggerWriterTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Logger\LoggerWriter::__construct()
     * @covers \ExtendsFramework\Logger\LoggerWriter::getWriter()
     * @covers \ExtendsFramework\Logger\LoggerWriter::mustInterrupt()
     */
    public function testGetMethods(): void
    {
        $writer = $this->createMock(WriterInterface::class);

        /**
         * @var WriterInterface $writer
         */
        $loggerWriter = new LoggerWriter($writer, true);

        $this->assertSame($writer, $loggerWriter->getWriter());
        $this->assertTrue($loggerWriter->mustInterrupt());
    }
}
