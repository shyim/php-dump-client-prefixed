<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as DriverStatement;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type;
use function is_string;
/**
 * A database abstraction-level statement that implements support for logging, DBAL mapping types, etc.
 */
class Statement
{
    /**
     * The SQL statement.
     *
     * @var string
     */
    protected $sql;
    /**
     * The bound parameters.
     *
     * @var mixed[]
     */
    protected $params = [];
    /**
     * The parameter types.
     *
     * @var int[]|string[]
     */
    protected $types = [];
    /**
     * The underlying driver statement.
     *
     * @var DriverStatement
     */
    protected $stmt;
    /**
     * The underlying database platform.
     *
     * @var AbstractPlatform
     */
    protected $platform;
    /**
     * The connection this statement is bound to and executed on.
     *
     * @var Connection
     */
    protected $conn;
    /**
     * Creates a new <tt>Statement</tt> for the given SQL and <tt>Connection</tt>.
     *
     * @internal The statement can be only instantiated by {@link Connection}.
     *
     * @param string     $sql  The SQL of the statement.
     * @param Connection $conn The connection on which the statement should be executed.
     *
     * @throws Exception
     */
    public function __construct($sql, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn)
    {
        $driverConnection = $conn->getWrappedConnection();
        try {
            $stmt = $driverConnection->prepare($sql);
        } catch (\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $ex) {
            throw $conn->convertExceptionDuringQuery($ex, $sql);
        }
        $this->sql = $sql;
        $this->stmt = $stmt;
        $this->conn = $conn;
        $this->platform = $conn->getDatabasePlatform();
    }
    /**
     * Binds a parameter value to the statement.
     *
     * The value can optionally be bound with a DBAL mapping type.
     * If bound with a DBAL mapping type, the binding type is derived from the mapping
     * type and the value undergoes the conversion routines of the mapping type before
     * being bound.
     *
     * @param string|int $param The name or position of the parameter.
     * @param mixed      $value The value of the parameter.
     * @param mixed      $type  Either a PDO binding type or a DBAL mapping type name or instance.
     *
     * @return bool TRUE on success, FALSE on failure.
     *
     * @throws Exception
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        $this->params[$param] = $value;
        $this->types[$param] = $type;
        $bindingType = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING;
        if ($type !== null) {
            if (\is_string($type)) {
                $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type::getType($type);
            }
            $bindingType = $type;
            if ($type instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type) {
                $value = $type->convertToDatabaseValue($value, $this->platform);
                $bindingType = $type->getBindingType();
            }
        }
        try {
            return $this->stmt->bindValue($param, $value, $bindingType);
        } catch (\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $e) {
            throw $this->conn->convertException($e);
        }
    }
    /**
     * Binds a parameter to a value by reference.
     *
     * Binding a parameter by reference does not support DBAL mapping types.
     *
     * @param string|int $param    The name or position of the parameter.
     * @param mixed      $variable The reference to the variable to bind.
     * @param int        $type     The binding type.
     * @param int|null   $length   Must be specified when using an OUT bind
     *                             so that PHP allocates enough memory to hold the returned value.
     *
     * @return bool TRUE on success, FALSE on failure.
     *
     * @throws Exception
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null)
    {
        $this->params[$param] = $variable;
        $this->types[$param] = $type;
        try {
            return $this->stmt->bindParam($param, $variable, $type, $length);
        } catch (\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $e) {
            throw $this->conn->convertException($e);
        }
    }
    /**
     * Executes the statement with the currently bound parameters.
     *
     * @param mixed[]|null $params
     *
     * @throws Exception
     */
    public function execute($params = null) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Result
    {
        if ($params !== null) {
            $this->params = $params;
        }
        $logger = $this->conn->getConfiguration()->getSQLLogger();
        if ($logger !== null) {
            $logger->startQuery($this->sql, $this->params, $this->types);
        }
        try {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Result($this->stmt->execute($params), $this->conn);
        } catch (\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $ex) {
            throw $this->conn->convertExceptionDuringQuery($ex, $this->sql, $this->params, $this->types);
        } finally {
            if ($logger !== null) {
                $logger->stopQuery();
            }
        }
    }
    /**
     * Gets the wrapped driver statement.
     *
     * @return DriverStatement
     */
    public function getWrappedStatement()
    {
        return $this->stmt;
    }
}
