<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractOracleDriver\EasyConnectString;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\OCI;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\OraclePlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\OracleSchemaManager;
/**
 * Abstract base implementation of the {@link Driver} interface for Oracle based drivers.
 */
abstract class AbstractOracleDriver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\OraclePlatform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\OracleSchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\OCI\ExceptionConverter();
    }
    /**
     * Returns an appropriate Easy Connect String for the given parameters.
     *
     * @param mixed[] $params The connection parameters to return the Easy Connect String for.
     *
     * @return string
     */
    protected function getEasyConnectString(array $params)
    {
        return (string) \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractOracleDriver\EasyConnectString::fromConnectionParameters($params);
    }
}
