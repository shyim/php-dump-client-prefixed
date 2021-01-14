<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use function array_merge;
use function func_get_args;
use function is_array;
/**
 * Event Arguments used when SQL queries for creating table columns are generated inside {@link AbstractPlatform}.
 */
class SchemaCreateTableColumnEventArgs extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Event\SchemaEventArgs
{
    /** @var Column */
    private $column;
    /** @var Table */
    private $table;
    /** @var AbstractPlatform */
    private $platform;
    /** @var string[] */
    private $sql = [];
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->column = $column;
        $this->table = $table;
        $this->platform = $platform;
    }
    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
    }
    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
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
     * @return SchemaCreateTableColumnEventArgs
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
