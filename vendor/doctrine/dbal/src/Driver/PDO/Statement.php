<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception as ExceptionInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception\UnknownParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use PDO;
use PDOException;
use PDOStatement;
use function array_slice;
use function func_get_args;
final class Statement implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
{
    private const PARAM_TYPE_MAP = [\_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::NULL => \PDO::PARAM_NULL, \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::INTEGER => \PDO::PARAM_INT, \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING => \PDO::PARAM_STR, \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::ASCII => \PDO::PARAM_STR, \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BINARY => \PDO::PARAM_LOB, \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT => \PDO::PARAM_LOB, \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BOOLEAN => \PDO::PARAM_BOOL];
    /** @var PDOStatement */
    private $stmt;
    /**
     * @internal The statement can be only instantiated by its driver connection.
     */
    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        $type = $this->convertParamType($type);
        try {
            return $this->stmt->bindValue($param, $value, $type);
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    /**
     * {@inheritDoc}
     *
     * @param mixed    $param
     * @param mixed    $variable
     * @param int      $type
     * @param int|null $length
     * @param mixed    $driverOptions
     *
     * @return bool
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null, $driverOptions = null)
    {
        $type = $this->convertParamType($type);
        try {
            return $this->stmt->bindParam($param, $variable, $type, ...\array_slice(\func_get_args(), 3));
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function execute($params = null) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        try {
            $this->stmt->execute($params);
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Result($this->stmt);
    }
    /**
     * Converts DBAL parameter type to PDO parameter type
     *
     * @param int $type Parameter type
     *
     * @throws ExceptionInterface
     */
    private function convertParamType(int $type) : int
    {
        if (!isset(self::PARAM_TYPE_MAP[$type])) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception\UnknownParameterType::new($type);
        }
        return self::PARAM_TYPE_MAP[$type];
    }
}
