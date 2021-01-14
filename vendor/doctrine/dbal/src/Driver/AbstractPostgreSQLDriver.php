<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\PostgreSQL;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\VersionAwarePlatformDriver;
use function preg_match;
use function version_compare;
/**
 * Abstract base implementation of the {@link Driver} interface for PostgreSQL based drivers.
 */
abstract class AbstractPostgreSQLDriver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\VersionAwarePlatformDriver
{
    /**
     * {@inheritdoc}
     */
    public function createDatabasePlatformForVersion($version)
    {
        if (\preg_match('/^(?P<major>\\d+)(?:\\.(?P<minor>\\d+)(?:\\.(?P<patch>\\d+))?)?/', $version, $versionParts) === 0) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception::invalidPlatformVersionSpecified($version, '<major_version>.<minor_version>.<patch_version>');
        }
        $majorVersion = $versionParts['major'];
        $minorVersion = $versionParts['minor'] ?? 0;
        $patchVersion = $versionParts['patch'] ?? 0;
        $version = $majorVersion . '.' . $minorVersion . '.' . $patchVersion;
        if (\version_compare($version, '10.0', '>=')) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL100Platform();
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL94Platform();
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL94Platform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\PostgreSQLSchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\PostgreSQL\ExceptionConverter();
    }
}
