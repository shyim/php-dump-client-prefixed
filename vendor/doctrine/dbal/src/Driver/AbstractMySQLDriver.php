<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\MySQL;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MariaDb1027Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQL57Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQL80Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQLPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\MySQLSchemaManager;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\VersionAwarePlatformDriver;
use function preg_match;
use function stripos;
use function version_compare;
/**
 * Abstract base implementation of the {@link Driver} interface for MySQL based drivers.
 */
abstract class AbstractMySQLDriver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\VersionAwarePlatformDriver
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function createDatabasePlatformForVersion($version)
    {
        $mariadb = \stripos($version, 'mariadb') !== \false;
        if ($mariadb && \version_compare($this->getMariaDbMysqlVersionNumber($version), '10.2.7', '>=')) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MariaDb1027Platform();
        }
        if (!$mariadb) {
            $oracleMysqlVersion = $this->getOracleMysqlVersionNumber($version);
            if (\version_compare($oracleMysqlVersion, '8', '>=')) {
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQL80Platform();
            }
            if (\version_compare($oracleMysqlVersion, '5.7.9', '>=')) {
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQL57Platform();
            }
        }
        return $this->getDatabasePlatform();
    }
    /**
     * Get a normalized 'version number' from the server string
     * returned by Oracle MySQL servers.
     *
     * @param string $versionString Version string returned by the driver, i.e. '5.7.10'
     *
     * @throws Exception
     */
    private function getOracleMysqlVersionNumber(string $versionString) : string
    {
        if (\preg_match('/^(?P<major>\\d+)(?:\\.(?P<minor>\\d+)(?:\\.(?P<patch>\\d+))?)?/', $versionString, $versionParts) === 0) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception::invalidPlatformVersionSpecified($versionString, '<major_version>.<minor_version>.<patch_version>');
        }
        $majorVersion = $versionParts['major'];
        $minorVersion = $versionParts['minor'] ?? 0;
        $patchVersion = $versionParts['patch'] ?? null;
        if ($majorVersion === '5' && $minorVersion === '7' && $patchVersion === null) {
            $patchVersion = '9';
        }
        return $majorVersion . '.' . $minorVersion . '.' . $patchVersion;
    }
    /**
     * Detect MariaDB server version, including hack for some mariadb distributions
     * that starts with the prefix '5.5.5-'
     *
     * @param string $versionString Version string as returned by mariadb server, i.e. '5.5.5-Mariadb-10.0.8-xenial'
     *
     * @throws Exception
     */
    private function getMariaDbMysqlVersionNumber(string $versionString) : string
    {
        if (\preg_match('/^(?:5\\.5\\.5-)?(mariadb-)?(?P<major>\\d+)\\.(?P<minor>\\d+)\\.(?P<patch>\\d+)/i', $versionString, $versionParts) === 0) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception::invalidPlatformVersionSpecified($versionString, '^(?:5\\.5\\.5-)?(mariadb-)?<major_version>.<minor_version>.<patch_version>');
        }
        return $versionParts['major'] . '.' . $versionParts['minor'] . '.' . $versionParts['patch'];
    }
    /**
     * {@inheritdoc}
     *
     * @return MySQLPlatform
     */
    public function getDatabasePlatform()
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQLPlatform();
    }
    /**
     * {@inheritdoc}
     *
     * @return MySQLSchemaManager
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\MySQLSchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\MySQL\ExceptionConverter();
    }
}
