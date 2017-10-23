<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Alert;

use ExtendsFramework\Logger\Priority\AbstractPriority;

class AlertPriority extends AbstractPriority
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'alert';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Action must be taken immediately.';
    }
}
