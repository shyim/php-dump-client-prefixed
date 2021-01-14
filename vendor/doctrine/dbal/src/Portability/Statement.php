<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Portability;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as DriverStatement;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
/**
 * Portability wrapper for a Statement.
 */
final class Statement implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
{
    /** @var DriverStatement */
    private $stmt;
    /** @var Converter */
    private $converter;
    /**
     * Wraps <tt>Statement</tt> and applies portability measures.
     */
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement $stmt, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Converter $converter)
    {
        $this->stmt = $stmt;
        $this->converter = $converter;
    }
    /**
     * {@inheritdoc}
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null)
    {
        return $this->stmt->bindParam($param, $variable, $type, $length);
    }
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        return $this->stmt->bindValue($param, $value, $type);
    }
    /**
     * {@inheritdoc}
     */
    public function execute($params = null) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Result($this->stmt->execute($params), $this->converter);
    }
}
