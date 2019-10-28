<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\File;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Writer\AbstractWriter;
use ExtendsFramework\Logger\Writer\File\Exception\FileWriterFailed;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class FileWriter extends AbstractWriter
{
    /**
     * Location to write file to.
     *
     * @var string
     */
    private $location;

    /**
     * File format for date function.
     *
     * @var string|null
     */
    private $fileFormat;

    /**
     * Log message format.
     *
     * @var string|null
     */
    private $logFormat;

    /**
     * End of line character.
     *
     * @var string|null
     */
    private $newLine;

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
     * @throws ServiceLocatorException
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
            /** @var FilterInterface $service */
            $service = $serviceLocator->getService($filter['name'], $filter['options'] ?? []);
            $writer->addFilter($service);
        }

        foreach ($extra['decorators'] ?? [] as $decorator) {
            /** @var DecoratorInterface $service */
            $service = $serviceLocator->getService($decorator['name'], $decorator['options'] ?? []);
            $writer->addDecorator($service);
        }

        return $writer;
    }

    /**
     * @inheritDoc
     */
    public function write(LogInterface $log): WriterInterface
    {
        if (!$this->filter($log)) {
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
     * Get formatted message for $log.
     *
     * @param LogInterface $log
     * @return string
     */
    private function getFormattedMessage(LogInterface $log): string
    {
        $metaData = $log->getMetaData() ?: null;
        if (is_array($metaData)) {
            $metaData = json_encode($metaData, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_SLASHES);
        }

        $priority = $log->getPriority();
        $replacePairs = [
            '{datetime}' => $log->getDateTime()
                ->format(DATE_ATOM),
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
    private function getFileName(): string
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
    private function getLogFormat(): string
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
    private function getNewLine(): string
    {
        if ($this->newLine === null) {
            $this->newLine = PHP_EOL;
        }

        return $this->newLine;
    }

    /**
     * Get location.
     *
     * @return string
     */
    private function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Get file format.
     *
     * @return string|null
     */
    private function getFileFormat(): ?string
    {
        if ($this->fileFormat === null) {
            $this->fileFormat = 'Y-m-d';
        }

        return $this->fileFormat;
    }
}
