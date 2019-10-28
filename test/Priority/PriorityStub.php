<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority;

class PriorityStub extends AbstractPriority
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
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return '';
    }
}
