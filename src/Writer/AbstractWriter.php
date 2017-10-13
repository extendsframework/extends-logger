<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use SplPriorityQueue;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * Filters.
     *
     * @var SplPriorityQueue
     */
    protected $filters;

    /**
     * Decorators.
     *
     * @var SplPriorityQueue
     */
    protected $decorators;

    /**
     * Create SplPriorityQueue for filter and decorators.
     */
    public function __construct()
    {
        $this->filters = new SplPriorityQueue();
        $this->decorators = new SplPriorityQueue();
    }

    /**
     * Add filter.
     *
     * @param FilterInterface $filter
     * @param int|null        $priority
     * @return AbstractWriter
     */
    public function addFilter(FilterInterface $filter, int $priority = null): AbstractWriter
    {
        $this->filters->insert($filter, $priority ?: 1);

        return $this;
    }

    /**
     * Add decorator.
     *
     * @param DecoratorInterface $decorator
     * @param int|null           $priority
     * @return AbstractWriter
     */
    public function addDecorator(DecoratorInterface $decorator, int $priority = null): AbstractWriter
    {
        $this->decorators->insert($decorator, $priority ?: 1);

        return $this;
    }

    /**
     * Decorate $log and return new instance.
     *
     * @param LogInterface $log
     * @return LogInterface
     */
    protected function decorate(LogInterface $log): LogInterface
    {
        foreach ($this->decorators as $decorator) {
            if ($decorator instanceof DecoratorInterface) {
                $log = $decorator->decorate($log);
            }
        }

        return $log;
    }

    /**
     * Check if $log must be filtered.
     *
     * @param LogInterface $log
     * @return bool
     */
    protected function filter(LogInterface $log): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter instanceof FilterInterface && $filter->filter($log) === true) {
                return true;
            }
        }

        return false;
    }
}
