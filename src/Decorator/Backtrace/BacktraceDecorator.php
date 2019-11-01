<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Decorator\Backtrace;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\ServiceLocator\Resolver\StaticFactory\StaticFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class BacktraceDecorator implements DecoratorInterface, StaticFactoryInterface
{
    /**
     * Debug backtrace limit.
     *
     * @var int
     */
    private $limit;

    /**
     * Create backtrace decorator.
     *
     * @param int $limit
     */
    public function __construct(int $limit = null)
    {
        $this->limit = $limit ?? 6;
    }

    /**
     * @inheritDoc
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        return new static();
    }

    /**
     * @inheritDoc
     */
    public function decorate(LogInterface $log): LogInterface
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $this->limit);
        $call = end($backtrace);

        if (is_array($call)) {
            foreach ($call as $key => $value) {
                $log = $log->andMetaData($key, $value);
            }
        }

        return $log;
    }
}
