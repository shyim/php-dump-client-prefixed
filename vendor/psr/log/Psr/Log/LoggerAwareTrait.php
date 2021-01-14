<?php

namespace _PhpScoper3fe455fa007d\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(\_PhpScoper3fe455fa007d\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
