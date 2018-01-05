<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\File;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Writer\AbstractWriter;
use ExtendsFramework\Logger\Writer\File\Exception\FileWriterFailed;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class FileWriter extends AbstractWriter
{
    /**
     * Location to write file to.
     *
     * @var string
     */
    protected $location;

    /**
     * File format for date function.
     *
     * @var string
     */
    protected $fileFormat;

    /**
     * Log message format.
     *
     * @var string
     */
    protected $logFormat;

    /**
     * End of line character.
     *
     * @var string
     */
    protected $newLine;

    /**
     * FileWriter constructor.
     *
     * @param string      $location
     * @param string|null $fileFormat
     * @param string|null $logFormat
     * @param string|null $newLine
     */
    public function __construct(
        string $location,
        string $fileFormat = null,
        string $logFormat = null,
        string $newLine = null
    ) {
        $this->location = $location;
        $this->fileFormat = $fileFormat;
        $this->logFormat = $logFormat;
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
            $filename = $this->getFileName();

            $handle = fopen($filename, 'ab');
            if (fwrite($handle, $message . $this->getNewLine()) === false) {
                throw new FileWriterFailed($message, $filename);
            }

            fclose($handle);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        $writer = new static(
            $extra['location'],
            $extra['file_format'] ?? null,
            $extra['log_format'] ?? null,
            $extra['new_line'] ?? null
        );

        foreach ($extra['filters'] ?? [] as $filter) {
            $service = $serviceLocator->getService($filter['name'], $filter['options'] ?? []);

            /**
             * @var FilterInterface $service
             */
            $writer->addFilter($service);
        }

        foreach ($extra['decorators'] ?? [] as $decorator) {
            $service = $serviceLocator->getService($decorator['name'], $decorator['options'] ?? []);

            /**
             * @var DecoratorInterface $service
             */
            $writer->addDecorator($service);
        }

        return $writer;
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
            $metaData = json_encode($metaData, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_SLASHES);
        }

        $priority = $log->getPriority();
        $replacePairs = [
            '{datetime}' => $log->getDateTime()->format(DATE_ATOM),
            '{keyword}' => strtoupper($priority->getKeyword()),
            '{value}' => $priority->getValue(),
            '{message}' => $log->getMessage(),
            '{metaData}' => $metaData,
        ];

        return trim(strtr($this->getLogFormat(), $replacePairs));
    }

    /**
     * Return full path and filename.
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return sprintf(
            '%s/%s.log',
            rtrim($this->getLocation(), '/'),
            date($this->getFileFormat())
        );
    }

    /**
     * Get log format.
     *
     * @return string
     */
    protected function getLogFormat(): string
    {
        if ($this->logFormat === null) {
            $this->logFormat = '{datetime} {keyword} ({value}): {message} {metaData}';
        }

        return $this->logFormat;
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

    /**
     * Get location.
     *
     * @return string|null
     */
    protected function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Get file format.
     *
     * @return string|null
     */
    protected function getFileFormat(): ?string
    {
        return $this->fileFormat ?? 'Y-m-d';
    }
}
