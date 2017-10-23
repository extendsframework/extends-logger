<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority\Notice;

use ExtendsFramework\Logger\Priority\AbstractPriority;

class NoticePriority extends AbstractPriority
{
    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return 5;
    }

    /**
     * @inheritDoc
     */
    public function getKeyword(): string
    {
        return 'notice';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Normal but significant conditions.';
    }
}
