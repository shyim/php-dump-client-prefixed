<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;
/**
 * Event Arguments used when the SQL query for dropping tables are generated inside {@link AbstractPlatform}.
 */
class SchemaDropTableEventArgs extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Event\SchemaEventArgs
{
    /** @var string|Table */
    private $table;
    /** @var AbstractPlatform */
    private $platform;
    /** @var string|null */
    private $sql;
    /**
     * @param string|Table $table
     *
     * @throws InvalidArgumentException
     */
    public function __construct($table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->table = $table;
        $this->platform = $platform;
    }
    /**
     * @return string|Table
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
     * @param string $sql
     *
     * @return SchemaDropTableEventArgs
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }
    /**
     * @return string|null
     */
    public function getSql()
    {
        return $this->sql;
    }
}
