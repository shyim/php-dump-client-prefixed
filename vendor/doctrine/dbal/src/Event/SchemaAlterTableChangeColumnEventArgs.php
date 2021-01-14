<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ColumnDiff;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff;
use function array_merge;
use function func_get_args;
use function is_array;
/**
 * Event Arguments used when SQL queries for changing table columns are generated inside {@link AbstractPlatform}.
 */
class SchemaAlterTableChangeColumnEventArgs extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Event\SchemaEventArgs
{
    /** @var ColumnDiff */
    private $columnDiff;
    /** @var TableDiff */
    private $tableDiff;
    /** @var AbstractPlatform */
    private $platform;
    /** @var string[] */
    private $sql = [];
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ColumnDiff $columnDiff, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff $tableDiff, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->columnDiff = $columnDiff;
        $this->tableDiff = $tableDiff;
        $this->platform = $platform;
    }
    /**
     * @return ColumnDiff
     */
    public function getColumnDiff()
    {
        return $this->columnDiff;
    }
    /**
     * @return TableDiff
     */
    public function getTableDiff()
    {
        return $this->tableDiff;
    }
    /**
     * @return AbstractPlatform
     */
    public function getPlatform()
    {
        return $this->platform;
    }
    /**
     * Passing multiple SQL statements as an array is deprecated. Pass each statement as an individual argument instead.
     *
     * @param string|string[] $sql
     *
     * @return SchemaAlterTableChangeColumnEventArgs
     */
    public function addSql($sql)
    {
        $this->sql = \array_merge($this->sql, \is_array($sql) ? $sql : \func_get_args());
        return $this;
    }
    /**
     * @return string[]
     */
    public function getSql()
    {
        return $this->sql;
    }
}