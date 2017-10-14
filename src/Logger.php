<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Logger\Writer\WriterException;
use ExtendsFramework\Logger\Writer\WriterInterface;

class Logger implements LoggerInterface
{
    /**
     * Writer queue.
     *
     * @var LoggerWriter[]
     */
    protected $writers = [];

    /**
     * @inheritDoc
     */
    public function log(string $message, PriorityInterface $priority = null, array $metaData = null): LoggerInterface
    {
        $log = $this->getLog($message, $priority, $metaData);
        foreach ($this->writers as $writer) {
            try {
                $writer->getWriter()->write($log);
                if ($writer->mustInterrupt() === true) {
                    break;
                }
            } catch (WriterException $exception) {
                syslog(LOG_CRIT, $exception->getMessage());
            }
        }

        return $this;
    }

    /**
     * Add $writer to logger.
     *
     * When $interrupt is true and the writer's write method will not throw an exception, the next writer won't be
     * called.
     *
     * @param WriterInterface $writer
     * @param bool|null       $interrupt
     * @return Logger
     */
    public function addWriter(WriterInterface $writer, bool $interrupt = null): Logger
    {
        $this->writers[] = new LoggerWriter($writer, $interrupt ?: false);

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