<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\File\Exception;

use Exception;
use ExtendsFramework\Logger\Writer\File\FileWriterException;

class FileWriterFailed extends Exception implements FileWriterException
{
    /**
     * Failed to write $message to $filename.
     *
     * @param string $message
     * @param string $filename
     */
    public function __construct(string $message, string $filename)
    {
        parent::__construct(sprintf(
            'Failed to write message "%s" to file "%s".',
            $message,
            $filename
        ));
    }
}
