<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Priority;

/**
 * @see https://tools.ietf.org/html/rfc3164
 */
interface PriorityInterface
{
    /**
     * Return priority value.
     *
     * @return int
     */
    public function getValue(): int;

    /**
     * Return priority keyword.
     *
     * @return string
     */
    public function getKeyword(): string;

    /**
     * Return priority description.
     *
     * @return string
     */
    public function getDescription(): string;
}
