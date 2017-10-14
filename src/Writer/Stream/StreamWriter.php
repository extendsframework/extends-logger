<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\Stream;

use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Writer\AbstractWriter;
use ExtendsFramework\Logger\Writer\Stream\Exception\StreamWriteFailed;
use ExtendsFramework\Logger\Writer\WriterInterface;

class StreamWriter extends AbstractWriter
{
    /**
     * Stream to write.
     *
     * @var string
     */
    protected $stream;

    /**
     * Log message format.
     *
     * @var string
     */
    protected $format = '{datetime} {keyword} ({value}): {message} {metaData}';

    /**
     * End of line character.
     *
     * @var string
     */
    protected $endOfLine = PHP_EOL;

    /**
     * Create new stream writer.
     *
     * @param string $stream
     */
    public function __construct(string $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    public function write(LogInterface $log): WriterInterface
    {
        if ($this->filter($log) === false) {
            $log = $this->decorate($log);
            $message = $this->getFormattedMessage($log);

            $handle = fopen($this->stream, 'ab');
            if (fwrite($handle, $message . $this->endOfLine) === false) {
                throw new StreamWriteFailed($message);
            }

            fclose($handle);
        }

        return $this;
    }

    /**
     * Get formatted message for $log.
     *
     * @param LogInterface $log
     * @return string
     */
    protected function getFormattedMessage(LogInterface $log): string
    {
        $metaData = $log->getMetaData() ?: null;
        if (is_array($metaData) === true) {
            $metaData = json_encode($metaData, JSON_PARTIAL_OUTPUT_ON_ERROR);
        }

        $priority = $log->getPriority();
        $replacePairs = [
            '{datetime}' => $log->getDateTime()->format(DATE_ATOM),
            '{keyword}' => strtoupper($priority->getKeyword()),
            '{value}' => $priority->getValue(),
            '{message}' => $log->getMessage(),
            '{metaData}' => $metaData,
        ];

        return trim(strtr($this->format, $replacePairs));
    }
}
