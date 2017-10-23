<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Error;

use ExtendsFramework\Logger\Priority\AbstractPriority;

class ErrorPriority extends AbstractPriority
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 3;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'err';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Error conditions.';
    }
}
