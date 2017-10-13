<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use DateTime;
use ExtendsFramework\Logger\Priority\PriorityInterface;

interface LogInterface
{
    /**
     * Actual log message.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Log priority.
     *
     * @return PriorityInterface
     */
    public function getPriority(): PriorityInterface;

    /**
     * Datetime when log happened.
     *
     * @return DateTime
     */
    public function getDateTime(): DateTime;

    /**
     * Get extra meta data.
     *
     * @return array
     */
    public function getMetaData(): array;

    /**
     * Return new log with $message.
     *
     * @param string $message
     * @return LogInterface
     */
    public function withMessage(string $message): LogInterface;

    /**
     * Return new log with $metaData.
     *
     * @param array $metaData
     * @return LogInterface
     */
    public function withMetaData(array $metaData): LogInterface;

    /**
     * Return new log with $key and $value added to the meta data.
     *
     * @param string $key
     * @param mixed  $value
     * @return LogInterface
     */
    public function andMetaData(string $key, $value): LogInterface;
}
