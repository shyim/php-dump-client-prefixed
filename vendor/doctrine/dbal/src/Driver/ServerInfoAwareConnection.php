<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

/**
 * Contract for a connection that is able to provide information about the server it is connected to.
 */
interface ServerInfoAwareConnection extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Connection
{
    /**
     * Returns the version number of the database server connected to.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getServerVersion();
}
