<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\Http\Middleware\Logger;

use Exception;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Logger\LoggerInterface;
use PHPUnit\Framework\TestCase;

class LoggerMiddlewareTest extends TestCase
{
    /**
     * Process.
     *
     * Test that response from chain will be returned.
     *
     * @covers \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::__construct()
     * @covers \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::process()
     * @covers \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::getLogger()
     */
    public function testProcess(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willReturn($this->createMock(ResponseInterface::class));

        /**
         * @var LoggerInterface          $logger
         * @var MiddlewareChainInterface $chain
         * @var RequestInterface         $request
         */
        $middleware = new LoggerMiddleware($logger);
        $response = $middleware->process($request, $chain);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * Log.
     *
     * Test that exception will be caught and message will be logged.
     *
     * @covers                   \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::__construct()
     * @covers                   \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::process()
     * @covers                   \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::getLogger()
     * @expectedException        \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\MiddlewareExceptionStub
     * @expectedExceptionMessage Fancy exception message!
     */
    public function testLog(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('log')
            ->with('Fancy exception message!');

        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willThrowException(new MiddlewareExceptionStub('Fancy exception message!'));

        /**
         * @var LoggerInterface          $logger
         * @var MiddlewareChainInterface $chain
         * @var RequestInterface         $request
         */
        $middleware = new LoggerMiddleware($logger);
        $middleware->process($request, $chain);
    }
}

class MiddlewareExceptionStub extends Exception
{
}
