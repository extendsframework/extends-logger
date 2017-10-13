<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer\Stream\Exception;

use Exception;
use ExtendsFramework\Logger\Writer\Stream\StreamWriterException;

class StreamWriteFailed extends Exception implements StreamWriterException
{
    /**
     * Failed to write $message to stream.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct(sprintf(
            'Failed to write message "%s" to stream writer.',
            $message
        ));
    }
}
