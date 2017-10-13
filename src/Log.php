<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use DateTime;
use ExtendsFramework\Logger\Priority\Critical\CriticalPriority;
use ExtendsFramework\Logger\Priority\PriorityInterface;

class Log implements LogInterface
{
    /**
     * Actual message.
     *
     * @var string
     */
    protected $message;

    /**
     * Log priority.
     *
     * @var PriorityInterface
     */
    protected $priority;

    /**
     * Datetime when log happened.
     *
     * @var DateTime
     */
    protected $datetime;

    /**
     * Extra meta data.
     *
     * @var array
     */
    protected $metaData;

    /**
     * Create new log.
     *
     * @param string            $message
     * @param PriorityInterface $priority
     * @param DateTime          $datetime
     * @param array             $metaData
     */
    public function __construct(string $message, PriorityInterface $priority = null, DateTime $datetime = null, array $metaData = null)
    {
        $this->message = $message;
        $this->priority = $priority ?: new CriticalPriority();
        $this->datetime = $datetime ?: new DateTime();
        $this->metaData = $metaData ?: [];
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): PriorityInterface
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function getDateTime(): DateTime
    {
        return $this->datetime;
    }

    /**
     * @inheritDoc
     */
    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * @inheritDoc
     */
    public function withMessage(string $message): LogInterface
    {
        $log = clone $this;
        $log->message = $message;

        return $log;
    }

    /**
     * @inheritDoc
     */
    public function withMetaData(array $metaData): LogInterface
    {
        $log = clone $this;
        $log->metaData = $metaData;

        return $log;
    }

    /**
     * @inheritDoc
     */
    public function andMetaData(string $key, $value): LogInterface
    {
        $log = clone $this;
        $log->metaData[$key] = $value;

        return $log;
    }
}
