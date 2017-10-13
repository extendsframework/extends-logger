<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Decorator\Backtrace;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\LogInterface;

class BacktraceDecorator implements DecoratorInterface
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
     * @param int $depth
     */
    public function __construct(int $depth = null)
    {
        $this->limit = $depth ?: 6;
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
    public static function create(array $config): DecoratorInterface
    {
        return new static($config['limit'] ?? null);
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
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $this->limit);
    }
}
