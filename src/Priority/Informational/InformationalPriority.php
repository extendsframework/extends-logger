<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Informational;

use ExtendsFramework\Logger\Priority\PriorityInterface;

class InformationalPriority implements PriorityInterface
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 6;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'info';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Informational messages.';
    }
}
