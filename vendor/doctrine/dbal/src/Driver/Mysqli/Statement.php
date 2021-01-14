<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception\UnknownParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\ConnectionError;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\FailedReadingStreamOffset;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\NonStreamResourceUsedAsLargeObject;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\StatementError;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use mysqli;
use mysqli_stmt;
use function array_fill;
use function assert;
use function count;
use function feof;
use function fread;
use function get_resource_type;
use function is_int;
use function is_resource;
use function str_repeat;
final class Statement implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
{
    /** @var string[] */
    protected static $_paramTypeMap = [\_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::ASCII => 's', \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING => 's', \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BINARY => 's', \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BOOLEAN => 'i', \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::NULL => 's', \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::INTEGER => 'i', \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT => 'b'];
    /** @var mysqli */
    protected $_conn;
    /** @var mysqli_stmt */
    protected $_stmt;
    /** @var mixed[] */
    protected $_bindedValues;
    /** @var string */
    protected $types;
    /**
     * Contains ref values for bindValue().
     *
     * @var mixed[]
     */
    protected $_values = [];
    /**
     * @internal The statement can be only instantiated by its driver connection.
     *
     * @param string $prepareString
     *
     * @throws Exception
     */
    public function __construct(\mysqli $conn, $prepareString)
    {
        $this->_conn = $conn;
        $stmt = $conn->prepare($prepareString);
        if ($stmt === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\ConnectionError::new($this->_conn);
        }
        $this->_stmt = $stmt;
        $paramCount = $this->_stmt->param_count;
        if (0 >= $paramCount) {
            return;
        }
        $this->types = \str_repeat('s', $paramCount);
        $this->_bindedValues = \array_fill(1, $paramCount, null);
    }
    /**
     * {@inheritdoc}
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null)
    {
        \assert(\is_int($param));
        if (!isset(self::$_paramTypeMap[$type])) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception\UnknownParameterType::new($type);
        }
        $this->_bindedValues[$param] =& $variable;
        $this->types[$param - 1] = self::$_paramTypeMap[$type];
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        \assert(\is_int($param));
        if (!isset(self::$_paramTypeMap[$type])) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception\UnknownParameterType::new($type);
        }
        $this->_values[$param] = $value;
        $this->_bindedValues[$param] =& $this->_values[$param];
        $this->types[$param - 1] = self::$_paramTypeMap[$type];
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function execute($params = null) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        if ($this->_bindedValues !== null) {
            if ($params !== null) {
                if (!$this->bindUntypedValues($params)) {
                    throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\StatementError::new($this->_stmt);
                }
            } else {
                $this->bindTypedParameters();
            }
        }
        if (!$this->_stmt->execute()) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\StatementError::new($this->_stmt);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Result($this->_stmt);
    }
    /**
     * Binds parameters with known types previously bound to the statement
     *
     * @throws Exception
     */
    private function bindTypedParameters() : void
    {
        $streams = $values = [];
        $types = $this->types;
        foreach ($this->_bindedValues as $parameter => $value) {
            \assert(\is_int($parameter));
            if (!isset($types[$parameter - 1])) {
                $types[$parameter - 1] = static::$_paramTypeMap[\_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING];
            }
            if ($types[$parameter - 1] === static::$_paramTypeMap[\_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT]) {
                if (\is_resource($value)) {
                    if (\get_resource_type($value) !== 'stream') {
                        throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\NonStreamResourceUsedAsLargeObject::new($parameter);
                    }
                    $streams[$parameter] = $value;
                    $values[$parameter] = null;
                    continue;
                }
                $types[$parameter - 1] = static::$_paramTypeMap[\_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING];
            }
            $values[$parameter] = $value;
        }
        if (!$this->_stmt->bind_param($types, ...$values)) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\StatementError::new($this->_stmt);
        }
        $this->sendLongData($streams);
    }
    /**
     * Handle $this->_longData after regular query parameters have been bound
     *
     * @param array<int, resource> $streams
     *
     * @throws Exception
     */
    private function sendLongData(array $streams) : void
    {
        foreach ($streams as $paramNr => $stream) {
            while (!\feof($stream)) {
                $chunk = \fread($stream, 8192);
                if ($chunk === \false) {
                    throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\FailedReadingStreamOffset::new($paramNr);
                }
                if (!$this->_stmt->send_long_data($paramNr - 1, $chunk)) {
                    throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\StatementError::new($this->_stmt);
                }
            }
        }
    }
    /**
     * Binds a array of values to bound parameters.
     *
     * @param mixed[] $values
     *
     * @return bool
     */
    private function bindUntypedValues(array $values)
    {
        $params = [];
        $types = \str_repeat('s', \count($values));
        foreach ($values as &$v) {
            $params[] =& $v;
        }
        return $this->_stmt->bind_param($types, ...$params);
    }
}
