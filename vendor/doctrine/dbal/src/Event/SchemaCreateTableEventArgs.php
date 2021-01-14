<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use function array_merge;
use function func_get_args;
use function is_array;
/**
 * Event Arguments used when SQL queries for creating tables are generated inside {@link AbstractPlatform}.
 */
class SchemaCreateTableEventArgs extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Event\SchemaEventArgs
{
    /** @var Table */
    private $table;
    /** @var mixed[][] */
    private $columns;
    /** @var mixed[] */
    private $options;
    /** @var AbstractPlatform */
    private $platform;
    /** @var string[] */
    private $sql = [];
    /**
     * @param mixed[][] $columns
     * @param mixed[]   $options
     */
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, array $columns, array $options, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->options = $options;
        $this->platform = $platform;
    }
    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }
    /**
     * @return mixed[][]
     */
    public function getColumns()
    {
        return $this->columns;
    }
    /**
     * @return mixed[]
     */
    public function getOptions()
    {
        return $this->options;
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
     * @return SchemaCreateTableEventArgs
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
