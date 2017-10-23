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
    protected $limit;

    /**
     * Create backtrace decorator.
     *
     * @param int $limit
     */
    public function __construct(int $limit = null)
    {
        $this->limit = $limit;
    }

    /**
     * @inheritDoc
     */
    public function decorate(LogInterface $log): LogInterface
    {
        $backtrace = $this->getBacktrace();
        $call = end($backtrace);

        if (is_array($call) === true) {
            foreach ($call as $key => $value) {
                $log = $log->andMetaData($key, $value);
            }
        }

        return $log;
    }

    /**
     * @inheritDoc
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): DecoratorInterface
    {
        return new static();
    }

    /**
     * Get debug backtrace.
     *
     * Limit the backtrace to the call where the log was created.
     *
     * @return array
     */
    protected function getBacktrace(): array
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $this->getLimit());
    }

    /**
     * Get backtrace limit.
     *
     * @return int
     */
    protected function getLimit(): int
    {
        if ($this->limit === null) {
            $this->limit = 6;
        }

        return $this->limit;
    }
}
