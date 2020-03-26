<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\Http\Middleware\Logger;

use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Middleware\MiddlewareInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Logger\Exception\LoggedException;
use ExtendsFramework\Logger\Exception\LoggedExceptionInterface;
use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\Logger\Priority\Error\ErrorPriority;
use Throwable;

class LoggerMiddleware implements MiddlewareInterface
{
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggerMiddleware constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws LoggedExceptionInterface
     */
    public function process(RequestInterface $request, MiddlewareChainInterface $chain): ResponseInterface
    {
        try {
            return $chain->proceed($request);
        } catch (Throwable $exception) {
            $reference = uniqid();

            $this->logger->log($exception->getMessage(), new ErrorPriority(), [
                'reference' => $reference,
            ]);

            throw new LoggedException($exception, $reference);
        }
    }
}
