<?php

namespace _PhpScoper3fe455fa007d\Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\_PhpScoper3fe455fa007d\Psr\Log\LoggerInterface $logger);
}
