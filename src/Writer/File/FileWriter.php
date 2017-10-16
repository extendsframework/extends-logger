<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\File;

use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Writer\AbstractWriter;
use ExtendsFramework\Logger\Writer\File\Exception\FileWriterFailed;
use ExtendsFramework\Logger\Writer\WriterInterface;

class FileWriter extends AbstractWriter
{
    /**
     * Filename to write.
     *
     * @var string
     */
    protected $filename;

    /**
     * Log message format.
     *
     * @var string
     */
    protected $format;

    /**
     * End of line character.
     *
     * @var string
     */
    protected $newLine;

    /**
     * FileWriter constructor.
     *
     * @param string      $filename
     * @param string|null $format
     * @param string|null $newLine
     */
    public function __construct(string $filename, string $format = null, string $newLine = null)
    {
        $this->filename = $filename;
        $this->format = $format;
        $this->newLine = $newLine;
    }

    /**
     * @inheritDoc
     */
    public function write(LogInterface $log): WriterInterface
    {
        if ($this->filter($log) === false) {
            $log = $this->decorate($log);
            $message = $this->getFormattedMessage($log);

            $handle = fopen($this->filename, 'ab');
            if (fwrite($handle, $message . $this->getNewLine()) === false) {
                throw new FileWriterFailed($message, $this->filename);
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

        return trim(strtr($this->getFormat(), $replacePairs));
    }

    /**
     * Get log format.
     *
     * @return string
     */
    protected function getFormat(): string
    {
        if ($this->format === null) {
            $this->format = '{datetime} {keyword} ({value}): {message} {metaData}';
        }

        return $this->format;
    }

    /**
     * Get newline character(s).
     *
     * @return string
     */
    protected function getNewLine(): string
    {
        if ($this->newLine === null) {
            $this->newLine = PHP_EOL;
        }

        return $this->newLine;
    }
}
