<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Exception;

use Exception;
use Throwable;

class LoggedException extends Exception implements LoggedExceptionInterface
{
    /**
     * Reference.
     *
     * @var string
     */
    private $reference;

    /**
     * LoggedException constructor.
     *
     * @param Throwable $throwable
     * @param string $reference
     */
    public function __construct(Throwable $throwable, string $reference)
    {
        parent::__construct($throwable->getMessage(), $throwable->getCode(), $throwable);

        $this->reference = $reference;
    }

    /**
     * @inheritDoc
     */
    public function getReference(): string
    {
        return $this->reference;
    }
}
