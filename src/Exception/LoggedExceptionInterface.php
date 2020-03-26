<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Exception;

use Throwable;

interface LoggedExceptionInterface extends Throwable
{
    /**
     * Get reference for logged exception.
     *
     * @return string
     */
    public function getReference(): string;
}
