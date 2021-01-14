<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema;

use function in_array;
/**
 * Represents the change of a column.
 */
class ColumnDiff
{
    /** @var string */
    public $oldColumnName;
    /** @var Column */
    public $column;
    /** @var string[] */
    public $changedProperties = [];
    /** @var Column|null */
    public $fromColumn;
    /**
     * @param string   $oldColumnName
     * @param string[] $changedProperties
     */
    public function __construct($oldColumnName, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $column, array $changedProperties = [], ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $fromColumn = null)
    {
        $this->oldColumnName = $oldColumnName;
        $this->column = $column;
        $this->changedProperties = $changedProperties;
        $this->fromColumn = $fromColumn;
    }
    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function hasChanged($propertyName)
    {
        return \in_array($propertyName, $this->changedProperties, \true);
    }
    /**
     * @return Identifier
     */
    public function getOldColumnName()
    {
        $quote = $this->fromColumn !== null && $this->fromColumn->isQuoted();
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Identifier($this->oldColumnName, $quote);
    }
}
