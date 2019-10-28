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
    private $message;

    /**
     * Log priority.
     *
     * @var PriorityInterface|null
     */
    private $priority;

    /**
     * Datetime when log happened.
     *
     * @var DateTime|null
     */
    private $datetime;

    /**
     * Extra meta data.
     *
     * @var array|null
     */
    private $metaData;

    /**
     * Create new log.
     *
     * @param string            $message
     * @param PriorityInterface $priority
     * @param DateTime          $datetime
     * @param array             $metaData
     */
    public function __construct(
        string $message,
        PriorityInterface $priority = null,
        DateTime $datetime = null,
        array $metaData = null
    ) {
        $this->message = $message;
        $this->priority = $priority;
        $this->datetime = $datetime;
        $this->metaData = $metaData;
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
        if ($this->priority === null) {
            $this->priority = new CriticalPriority();
        }

        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function getDateTime(): DateTime
    {
        if ($this->datetime === null) {
            $this->datetime = new DateTime();
        }

        return $this->datetime;
    }

    /**
     * @inheritDoc
     */
    public function getMetaData(): array
    {
        if ($this->metaData === null) {
            $this->metaData = [];
        }

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
