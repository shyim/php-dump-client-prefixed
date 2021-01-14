<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\SQLSrv;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Statement as PDOStatement;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use PDO;
final class Statement implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
{
    /** @var PDOStatement */
    private $statement;
    /**
     * @internal The statement can be only instantiated by its driver connection.
     */
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Statement $statement)
    {
        $this->statement = $statement;
    }
    /**
     * {@inheritdoc}
     *
     * @param mixed $driverOptions
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null, $driverOptions = null)
    {
        switch ($type) {
            case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT:
            case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BINARY:
                if ($driverOptions === null) {
                    $driverOptions = \PDO::SQLSRV_ENCODING_BINARY;
                }
                break;
            case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::ASCII:
                $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING;
                $length = 0;
                $driverOptions = \PDO::SQLSRV_ENCODING_SYSTEM;
                break;
        }
        return $this->statement->bindParam($param, $variable, $type, $length, $driverOptions);
    }
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        return $this->bindParam($param, $value, $type);
    }
    /**
     * {@inheritdoc}
     */
    public function execute($params = null) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        return $this->statement->execute($params);
    }
}
