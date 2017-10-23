<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Warning;

use ExtendsFramework\Logger\Priority\AbstractPriority;

class WarningPriority extends AbstractPriority
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 4;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'warning';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Warning conditions.';
    }
}
