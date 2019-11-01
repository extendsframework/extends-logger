<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\Pdo;

use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Writer\AbstractWriter;
use ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithError;
use ExtendsFramework\Logger\Writer\Pdo\Exception\StatementFailedWithException;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PDO;
use PDOException;

class PdoWriter extends AbstractWriter
{
    /**
     * PDO connection.
     *
     * @var PDO
     */
    private $pdo;

    /**
     * Database table.
     *
     * @var string
     */
    private $table;

    /**
     * PdoWriter constructor.
     *
     * @param PDO         $pdo
     * @param string|null $table
     */
    public function __construct(PDO $pdo, string $table = null)
    {
        $this->pdo = $pdo;
        $this->table = $table ?? 'log';
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
            $extra['table'] ?? null
        );

        foreach ($extra['filters'] ?? [] as $filter) {
            /** @noinspection PhpParamsInspection */
            $writer->addFilter($serviceLocator->getService($filter['name'], $filter['options'] ?? []));
        }

        foreach ($extra['decorators'] ?? [] as $decorator) {
            /** @noinspection PhpParamsInspection */
            $writer->addDecorator($serviceLocator->getService($decorator['name'], $decorator['options'] ?? []));
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

            /** @noinspection SqlResolve */
            /** @noinspection SqlNoDataSourceInspection */
            $statement = $this->pdo->prepare(sprintf(
                'INSERT INTO `%s` (`value`, `keyword`, `date_time`, `message`, `meta_data`) ' .
                'VALUES (:value, :keyword, :date_time, :message, :meta_data)',
                $this->table
            ));

            $metaData = $log->getMetaData() ?: null;
            if (is_array($metaData)) {
                $metaData = json_encode($metaData, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_SLASHES);
            }

            $priority = $log->getPriority();
            $parameters = [
                'value' => $priority->getValue(),
                'keyword' => strtoupper($priority->getKeyword()),
                'date_time' => $log
                    ->getDateTime()
                    ->format('Y-m-d H:i:s'),
                'message' => $log->getMessage(),
                'meta_data' => $metaData,
            ];

            try {
                $result = $statement->execute($parameters);
            } catch (PDOException $exception) {
                throw new StatementFailedWithException($exception, $log->getMessage());
            }

            if (!$result) {
                throw new StatementFailedWithError($statement->errorCode(), $log->getMessage());
            }
        }

        return $this;
    }
}
