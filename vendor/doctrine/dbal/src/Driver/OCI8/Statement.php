<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\Error;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\UnknownParameterIndex;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\SQL\Parser;
use function assert;
use function is_int;
use function is_resource;
use function oci_bind_by_name;
use function oci_execute;
use function oci_new_descriptor;
use function oci_parse;
use const OCI_B_BIN;
use const OCI_B_BLOB;
use const OCI_COMMIT_ON_SUCCESS;
use const OCI_D_LOB;
use const OCI_NO_AUTO_COMMIT;
use const OCI_TEMP_BLOB;
use const SQLT_CHR;
final class Statement implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
{
    /** @var resource */
    protected $_dbh;
    /** @var resource */
    protected $_sth;
    /** @var ExecutionMode */
    private $executionMode;
    /** @var string[] */
    protected $_paramMap = [];
    /**
     * Holds references to bound parameter values.
     *
     * This is a new requirement for PHP7's oci8 extension that prevents bound values from being garbage collected.
     *
     * @var mixed[]
     */
    private $boundValues = [];
    /**
     * Creates a new OCI8Statement that uses the given connection handle and SQL statement.
     *
     * @internal The statement can be only instantiated by its driver connection.
     *
     * @param resource $dbh   The connection handle.
     * @param string   $query The SQL query.
     *
     * @throws Exception
     */
    public function __construct($dbh, $query, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\ExecutionMode $executionMode)
    {
        $parser = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\SQL\Parser(\false);
        $visitor = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\ConvertPositionalToNamedPlaceholders();
        $parser->parse($query, $visitor);
        $stmt = \oci_parse($dbh, $visitor->getSQL());
        \assert(\is_resource($stmt));
        $this->_sth = $stmt;
        $this->_dbh = $dbh;
        $this->_paramMap = $visitor->getParameterMap();
        $this->executionMode = $executionMode;
    }
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        return $this->bindParam($param, $value, $type, null);
    }
    /**
     * {@inheritdoc}
     */
    public function bindParam($param, &$variable, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING, $length = null)
    {
        if (\is_int($param)) {
            if (!isset($this->_paramMap[$param])) {
                throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\UnknownParameterIndex::new($param);
            }
            $param = $this->_paramMap[$param];
        }
        if ($type === \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT) {
            $lob = \oci_new_descriptor($this->_dbh, \OCI_D_LOB);
            \assert($lob !== \false);
            $lob->writetemporary($variable, \OCI_TEMP_BLOB);
            $variable =& $lob;
        }
        $this->boundValues[$param] =& $variable;
        return \oci_bind_by_name($this->_sth, $param, $variable, $length ?? -1, $this->convertParameterType($type));
    }
    /**
     * Converts DBAL parameter type to oci8 parameter type
     */
    private function convertParameterType(int $type) : int
    {
        switch ($type) {
            case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::BINARY:
                return \OCI_B_BIN;
            case \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT:
                return \OCI_B_BLOB;
            default:
                return \SQLT_CHR;
        }
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
        if ($this->executionMode->isAutoCommitEnabled()) {
            $mode = \OCI_COMMIT_ON_SUCCESS;
        } else {
            $mode = \OCI_NO_AUTO_COMMIT;
        }
        $ret = @\oci_execute($this->_sth, $mode);
        if (!$ret) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception\Error::new($this->_sth);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Result($this->_sth);
    }
}
