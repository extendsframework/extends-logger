<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Debug;

use ExtendsFramework\Logger\Priority\AbstractPriority;

class DebugPriority extends AbstractPriority
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 7;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'debug';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Debug-level messages.';
    }
}
