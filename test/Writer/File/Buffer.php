<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\File;

class Buffer
{
    /**
     * @var string|null
     */
    protected static $filename;

    /**
     * @var string|null
     */
    protected static $value;

    /**
     * @return string|null
     */
    public static function getFilename(): ?string
    {
        return self::$filename;
    }

    /**
     * @param $filename
     */
    public static function setFilename($filename): void
    {
        self::$filename = $filename;
    }

    /**
     * @return string|null
     */
    public static function getValue(): ?string
    {
        return self::$value;
    }

    /**
     * @param string $value
     */
    public static function setValue(string $value): void
    {
        static::$value .= $value;
    }

    public static function reset(): void
    {
        static::$value = null;
        static::$filename = null;
    }
}
