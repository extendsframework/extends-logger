<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use ExtendsFramework\Logger\Priority\PriorityInterface;

interface LoggerInterface
{
    /**
     * Log $message with $priority and $metaData.
     *
     * When $priority is null a CRIT (2) priority will be used.
     *
     * @param string                 $message
     * @param PriorityInterface|null $priority
     * @param array|null             $metaData
     * @return LoggerInterface
     */
    public function log(string $message, PriorityInterface $priority = null, array $metaData = null): LoggerInterface;
}
