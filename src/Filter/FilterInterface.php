<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter;

use ExtendsFramework\Logger\LogInterface;

interface FilterInterface
{
    /**
     * Check if $log must be filtered.
     *
     * True is returned when $log must be filtered, false instead.
     *
     * @param LogInterface $log
     * @return bool
     */
    public function filter(LogInterface $log): bool;
}
