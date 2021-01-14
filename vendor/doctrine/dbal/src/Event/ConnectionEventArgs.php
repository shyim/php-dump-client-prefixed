<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event;

use _PhpScoper3fe455fa007d\Doctrine\Common\EventArgs;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
/**
 * Event Arguments used when a Driver connection is established inside Doctrine\DBAL\Connection.
 */
class ConnectionEventArgs extends \_PhpScoper3fe455fa007d\Doctrine\Common\EventArgs
{
    /** @var Connection */
    private $connection;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }
    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
