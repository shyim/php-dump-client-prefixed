<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
interface ConnectionProvider
{
    public function getDefaultConnection() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
    /**
     * @throws ConnectionNotFound in case a connection with the given name does not exist.
     */
    public function getConnection(string $name) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
}
