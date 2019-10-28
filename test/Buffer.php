<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger;

class Buffer
{
    /**
     * @var int|null
     */
    protected static $priority;

    /**
     * @var string|null
     */
    protected static $message;

    /**
     * @return string
     */
    public static function getMessage(): string
    {
        return static::$message;
    }

    /**
     * @return int
     */
    public static function getPriority(): int
    {
        return static::$priority;
    }

    /**
     * @param int    $priority
     * @param string $message
     */
    public static function set(int $priority, string $message): void
    {
        static::$priority = $priority;
        static::$message = $message;
    }

    public static function reset(): void
    {
        static::$priority = null;
        static::$message = null;
    }
}
