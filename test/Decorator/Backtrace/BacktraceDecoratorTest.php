<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Decorator\Backtrace;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\LogInterface;
use PHPUnit\Framework\TestCase;

class BacktraceDecoratorTest extends TestCase
{
    /**
     * Decorate.
     *
     * Test that log meta data will be decorated with (at least) a file name from the debug backtrace and that a new
     * instance will be returned.
     *
     * @covers \ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator::__construct()
     * @covers \ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator::decorate()
     * @covers \ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator::getBacktrace()
     * @covers \ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator::getLimit()
     */
    public function testDecorate(): void
    {
        $log = $this->createMock(LogInterface::class);
        $log
            ->expects($this->any())
            ->method('andMetaData')
            ->with('file');

        /**
         * @var LogInterface $log
         */
        $decorator = new BacktraceDecorator();

        $this->assertNotSame($log, $decorator->decorate($log));
    }

    /**
     * Create.
     *
     * Test that create method will return a DecoratorInterface instance.
     *
     * @covers \ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator::__construct()
     * @covers \ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator::create()
     */
    public function testCreate(): void
    {
        $decorator = BacktraceDecorator::create([
            'limit' => 5,
        ]);

        $this->assertInstanceOf(DecoratorInterface::class, $decorator);
    }
}
