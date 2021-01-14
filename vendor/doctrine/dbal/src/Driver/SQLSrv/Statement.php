<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv\Exception\Error;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use function assert;
use function is_int;
use function sqlsrv_execute;
use function sqlsrv_fetch;
use function sqlsrv_get_field;
use function sqlsrv_next_result;
use function _PhpScoper3fe455fa007d\SQLSRV_PHPTYPE_STREAM;
use function _PhpScoper3fe455fa007d\SQLSRV_PHPTYPE_STRING;
use function sqlsrv_prepare;
use function _PhpScoper3fe455fa007d\SQLSRV_SQLTYPE_VARBINARY;
use function stripos;
use const SQLSRV_ENC_BINARY;
use const SQLSRV_ENC_CHAR;
use const SQLSRV_PARAM_IN;
final class Statement implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
{
    /**
     * The SQLSRV Resource.
     *
     * @var resource
     */
    private $conn;
    /**
     * The SQL statement to execute.
     *
     * @var string
     */
    private $sql;
    /**
     * The SQLSRV statement resource.
     *
     * @var resource|null
     */
    private $stmt;
    /**
     * References to the variables bound as statement parameters.
     *
     * @var mixed
     */
    private $variables = [];
    /**
     * Bound parameter types.
     *
     * @var int[]
     */
    private $types = [];
    /**
     * The last insert ID.
     *
     * @var LastInsertId|null
     */
    private $lastInsertId;
    /**
     * Append to any INSERT query to retrieve the last insert id.
     */
    private const LAST_INSERT_ID_SQL = ';SELECT SCOPE_IDENTITY() AS LastInsertId;';
    /**
     * @internal The statement can be only instantiated by its driver connection.
     *
     * @param resource $conn
     * @param string   $sql
     */
    public function __construct($conn, $sql, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv\LastInsertId $lastInsertId = null)
    {
        $this->conn = $conn;
        $this->sql = $sql;
        if (\stripos($sql, 'INSERT INTO ') !== 0) {
            return;
        }
        $this->sql .= self::LAST_INSERT_ID_SQL;
        $this->lastInsertId = $lastInsertId;
    }
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        \assert(\is_int($param));
        $this->variables[$param] = $value;
        $this->types[$param] = $type;
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null)
    {
        \assert(\is_int($param));
        $this->variables[$param] =& $variable;
        $this->types[$param] = $type;
        // unset the statement resource if it exists as the new one will need to be bound to the new variable
        $this->stmt = null;
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function execute($params = null) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        if ($params !== null) {
            foreach ($params as $key => $val) {
                if (\is_int($key)) {
                    $this->bindValue($key + 1, $val);
                } else {
                    $this->bindValue($key, $val);
                }
            }
        }
        if ($this->stmt === null) {
            $this->stmt = $this->prepare();
        }
        if (!\sqlsrv_execute($this->stmt)) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv\Exception\Error::new();
        }
        if ($this->lastInsertId !== null) {
            \sqlsrv_next_result($this->stmt);
            \sqlsrv_fetch($this->stmt);
            $this->lastInsertId->setId(\sqlsrv_get_field($this->stmt, 0));
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv\Result($this->stmt);
    }
    /**
     * Prepares SQL Server statement resource
     *
     * @return resource
     *
     * @throws Exception
     */
    private function prepare()
    {
        $params = [];
        foreach ($this->variables as $column => &$variable) {
            switch ($this->types[$column]) {
                case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT:
                    $params[$column - 1] = [&$variable, \SQLSRV_PARAM_IN, \_PhpScoper3fe455fa007d\SQLSRV_PHPTYPE_STREAM(\SQLSRV_ENC_BINARY), \_PhpScoper3fe455fa007d\SQLSRV_SQLTYPE_VARBINARY('max')];
                    break;
                case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BINARY:
                    $params[$column - 1] = [&$variable, \SQLSRV_PARAM_IN, \_PhpScoper3fe455fa007d\SQLSRV_PHPTYPE_STRING(\SQLSRV_ENC_BINARY)];
                    break;
                case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::ASCII:
                    $params[$column - 1] = [&$variable, \SQLSRV_PARAM_IN, \_PhpScoper3fe455fa007d\SQLSRV_PHPTYPE_STRING(\SQLSRV_ENC_CHAR)];
                    break;
                default:
                    $params[$column - 1] =& $variable;
                    break;
            }
        }
        $stmt = \sqlsrv_prepare($this->conn, $this->sql, $params);
        if ($stmt === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv\Exception\Error::new();
        }
        return $stmt;
    }
}
