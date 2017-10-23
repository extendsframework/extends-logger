<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Emergency;

use ExtendsFramework\Logger\Priority\AbstractPriority;

class EmergencyPriority extends AbstractPriority
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'emerg';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'System is unusable.';
    }
}
