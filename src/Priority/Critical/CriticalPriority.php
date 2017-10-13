<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Critical;

use ExtendsFramework\Logger\Priority\PriorityInterface;

class CriticalPriority implements PriorityInterface
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 2;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'crit';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Critical conditions, such as hard device errors.';
    }
}
