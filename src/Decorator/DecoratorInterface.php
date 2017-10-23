<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Decorator;

use ExtendsFramework\Logger\LogInterface;

interface DecoratorInterface
{
    /**
     * Decorate $log.
     *
     * When $log is decorated, a new instance must be returned.
     *
     * @param LogInterface $log
     * @return LogInterface
     */
    public function decorate(LogInterface $log): LogInterface;
}
