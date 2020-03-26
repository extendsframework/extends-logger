<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\Http\Middleware\Logger;

use Exception;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Logger\Exception\LoggedExceptionInterface;
use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\Logger\Priority\Error\ErrorPriority;
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
         * @var LoggerInterface $logger
         * @var MiddlewareChainInterface $chain
         * @var RequestInterface $request
         */
        $middleware = new LoggerMiddleware($logger);
        $response = $middleware->process($request, $chain);

        $this->assertIsObject($response);
    }

    /**
     * Log.
     *
     * Test that exception will be caught and message will be logged.
     *
     * @covers \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::__construct()
     * @covers \ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware::process()
     */
    public function testLog(): void
    {
        $throwable = new Exception('Fancy exception message!', 123);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('log')
            ->with(
                'Fancy exception message!',
                $this->isInstanceOf(ErrorPriority::class),
                [
                    'reference' => '123456'
                ]
            );

        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willThrowException($throwable);

        /**
         * @var LoggerInterface $logger
         * @var MiddlewareChainInterface $chain
         * @var RequestInterface $request
         */
        $middleware = new LoggerMiddleware($logger);

        try {
            $middleware->process($request, $chain);
        } catch (LoggedExceptionInterface $exception) {
            $this->assertSame($throwable, $exception->getPrevious());
            $this->assertSame('123456', $exception->getReference());
        }
    }
}

/**
 * Overwrite method.
 *
 * @return string
 */
function uniqid(): string
{
    return '123456';
}
