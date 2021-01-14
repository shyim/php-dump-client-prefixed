<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event\Listeners;

use _PhpScoper3fe455fa007d\Doctrine\Common\EventSubscriber;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Event\ConnectionEventArgs;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Events;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
/**
 * Session init listener for executing a single SQL statement right after a connection is opened.
 */
class SQLSessionInit implements \_PhpScoper3fe455fa007d\Doctrine\Common\EventSubscriber
{
    /** @var string */
    protected $sql;
    /**
     * @param string $sql
     */
    public function __construct($sql)
    {
        $this->sql = $sql;
    }
    /**
     * @return void
     *
     * @throws Exception
     */
    public function postConnect(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Event\ConnectionEventArgs $args)
    {
        $args->getConnection()->executeStatement($this->sql);
    }
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [\_PhpScoper3fe455fa007d\Doctrine\DBAL\Events::postConnect];
    }
}
