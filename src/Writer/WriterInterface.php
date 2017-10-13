<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Writer;

use ExtendsFramework\Logger\LogInterface;

interface WriterInterface
{
    /**
     * Write $log.
     *
     * @param LogInterface $log
     * @return WriterInterface
     * @throws WriterException
     */
    public function write(LogInterface $log): WriterInterface;
}
