<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Logging;

/**
 * Chains multiple SQLLogger.
 */
class LoggerChain implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Logging\SQLLogger
{
    /** @var iterable<SQLLogger> */
    private $loggers = [];
    /**
     * @param iterable<SQLLogger> $loggers
     */
    public function __construct(iterable $loggers = [])
    {
        $this->loggers = $loggers;
    }
    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        foreach ($this->loggers as $logger) {
            $logger->startQuery($sql, $params, $types);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        foreach ($this->loggers as $logger) {
            $logger->stopQuery();
        }
    }
}
