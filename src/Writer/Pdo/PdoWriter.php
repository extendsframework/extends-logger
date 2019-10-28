<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\Pdo;

use ExtendsFramework\Logger\Decorator\DecoratorInterface;
use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Writer\AbstractWriter;
use ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithError;
use ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithException;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PDO;
use PDOException;
use PDOStatement;

class PdoWriter extends AbstractWriter
{
    /**
     * PDO connection.
     *
     * @var PDO
     */
    private $pdo;

    /**
     * PDO query string.
     *
     * @var string|null
     */
    private $queryString;

    /**
     * Parameter callback.
     *
     * @var callable|null
     */
    private $callback;

    /**
     * PdoWriter constructor.
     *
     * @param PDO           $pdo
     * @param string|null   $queryString
     * @param callable|null $callback
     */
    public function __construct(PDO $pdo, string $queryString = null, callable $callback = null)
    {
        $this->pdo = $pdo;
        $this->queryString = $queryString;
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     * @throws ServiceLocatorException
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        $pdo = $serviceLocator->getService(PDO::class);

        /**
         * @var PDO $pdo
         */
        $writer = new static(
            $pdo,
            $extra['query_string'] ?? null,
            $extra['callback'] ?? null
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
     * @inheritDoc
     */
    public function write(LogInterface $log): WriterInterface
    {
        if (!$this->filter($log)) {
            $log = $this->decorate($log);

            $statement = $this->getStatement();
            try {
                $result = $statement->execute($this->getParameters($log));
            } catch (PDOException $exception) {
                throw new StatementFailedWithException($exception, $log->getMessage());
            }

            if (!$result) {
                throw new StatementFailedWithError($statement->errorCode(), $log->getMessage());
            }
        }

        return $this;
    }

    /**
     * Get statement to execute.s
     *
     * @return PDOStatement
     */
    private function getStatement(): PDOStatement
    {
        return $this
            ->getPdo()
            ->prepare(trim($this->getQueryString()));
    }

    /**
     * Get statement parameters.
     *
     * @param LogInterface $log
     * @return array
     */
    private function getParameters(LogInterface $log): array
    {
        $callback = $this->getCallback();

        return $callback($log);
    }

    /**
     * Get parameter callback.
     *
     * @return callable
     */
    private function getCallback(): callable
    {
        if ($this->callback === null) {
            $this->callback = static function (LogInterface $log): array {
                $metaData = $log->getMetaData() ?: null;
                if (is_array($metaData)) {
                    $metaData = json_encode($metaData, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_SLASHES);
                }

                $priority = $log->getPriority();

                return [
                    'value' => $priority->getValue(),
                    'keyword' => strtoupper($priority->getKeyword()),
                    'date_time' => $log
                        ->getDateTime()
                        ->format('Y-m-d H:i:s'),
                    'message' => $log->getMessage(),
                    'meta_data' => $metaData,
                ];
            };
        }

        return $this->callback;
    }

    /**
     * Get statement query string.
     *
     * @return string
     */
    private function getQueryString(): string
    {
        if ($this->queryString === null) {
            $this->queryString = '
                INSERT INTO log (value, keyword, date_time, message, meta_data)
                VALUES (:value, :keyword, :date_time, :message, :meta_data)';
        }

        return $this->queryString;
    }

    /**
     * Get pdo.
     *
     * @return PDO
     */
    private function getPdo(): PDO
    {
        return $this->pdo;
    }
}
