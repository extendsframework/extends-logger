<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\WriterException;
use ExtendsFramework\Logger\Writer\WriterInterface;
use SplPriorityQueue;

class Logger implements LoggerInterface
{
    /**
     * Writer queue.
     *
     * @var SplPriorityQueue
     */
    protected $writers;

    /**
     * Create new logger.
     */
    public function __construct()
    {
        $this->writers = new SplPriorityQueue();
    }

    /**
     * @inheritDoc
     */
    public function log(string $message, PriorityInterface $priority = null, array $metaData = null): LoggerInterface
    {
        $log = $this->getLog($message, $priority, $metaData);
        foreach (clone $this->writers as $writer) {
            if ($writer instanceof WriterInterface) {
                try {
                    $writer->write($log);
                } catch (WriterException $exception) {
                    syslog(LOG_CRIT, $exception->getMessage());
                }
            }
        }

        return $this;
    }

    /**
     * Add $writer with $priority to logger.
     *
     * Writers with a higher $priority will be processed earlier. Default $priority is 1.
     *
     * @param WriterInterface $writer
     * @param int|null        $priority
     * @return Logger
     */
    public function addWriter(WriterInterface $writer, int $priority = null): Logger
    {
        $this->writers->insert($writer, $priority ?: 1);

        return $this;
    }

    /**
     * Get new log.
     *
     * @param string                 $message
     * @param PriorityInterface|null $priority
     * @param array|null             $metaData
     * @return LogInterface
     */
    protected function getLog(string $message, PriorityInterface $priority = null, array $metaData = null): LogInterface
    {
        return new Log($message, $priority ?? null, null, $metaData ?? null);
    }
}
