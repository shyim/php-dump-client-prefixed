<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\ConnectionFailed;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\Error;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\SequenceDoesNotExist;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as DriverStatement;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use function addcslashes;
use function assert;
use function is_float;
use function is_int;
use function oci_commit;
use function oci_connect;
use function oci_pconnect;
use function oci_rollback;
use function oci_server_version;
use function preg_match;
use function str_replace;
use const OCI_NO_AUTO_COMMIT;
final class Connection implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\ServerInfoAwareConnection
{
    /** @var resource */
    protected $dbh;
    /** @var ExecutionMode */
    private $executionMode;
    /**
     * Creates a Connection to an Oracle Database using oci8 extension.
     *
     * @internal The connection can be only instantiated by its driver.
     *
     * @param string $username
     * @param string $password
     * @param string $db
     * @param string $charset
     * @param int    $sessionMode
     * @param bool   $persistent
     *
     * @throws Exception
     */
    public function __construct($username, $password, $db, $charset = '', $sessionMode = \OCI_NO_AUTO_COMMIT, $persistent = \false)
    {
        $dbh = $persistent ? @\oci_pconnect($username, $password, $db, $charset, $sessionMode) : @\oci_connect($username, $password, $db, $charset, $sessionMode);
        if ($dbh === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\ConnectionFailed::new();
        }
        $this->dbh = $dbh;
        $this->executionMode = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\ExecutionMode();
    }
    /**
     * {@inheritdoc}
     */
    public function getServerVersion()
    {
        $version = \oci_server_version($this->dbh);
        if ($version === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\Error::new($this->dbh);
        }
        \assert(\preg_match('/\\s+(\\d+\\.\\d+\\.\\d+\\.\\d+\\.\\d+)\\s+/', $version, $matches) === 1);
        return $matches[1];
    }
    public function prepare(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Statement($this->dbh, $sql, $this->executionMode);
    }
    public function query(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        return $this->prepare($sql)->execute();
    }
    /**
     * {@inheritdoc}
     */
    public function quote($value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        if (\is_int($value) || \is_float($value)) {
            return $value;
        }
        $value = \str_replace("'", "''", $value);
        return "'" . \addcslashes($value, "\0\n\r\\\32") . "'";
    }
    public function exec(string $sql) : int
    {
        return $this->prepare($sql)->execute()->rowCount();
    }
    /**
     * {@inheritdoc}
     *
     * @return int|false
     */
    public function lastInsertId($name = null)
    {
        if ($name === null) {
            return \false;
        }
        $result = $this->query('SELECT ' . $name . '.CURRVAL FROM DUAL')->fetchOne();
        if ($result === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\SequenceDoesNotExist::new();
        }
        return (int) $result;
    }
    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->executionMode->disableAutoCommit();
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        if (!\oci_commit($this->dbh)) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\Error::new($this->dbh);
        }
        $this->executionMode->enableAutoCommit();
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function rollBack()
    {
        if (!\oci_rollback($this->dbh)) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\Error::new($this->dbh);
        }
        $this->executionMode->enableAutoCommit();
        return \true;
    }
}
