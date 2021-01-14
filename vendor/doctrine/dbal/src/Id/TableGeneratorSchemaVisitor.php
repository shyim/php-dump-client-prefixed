<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Id;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\Visitor;
class TableGeneratorSchemaVisitor implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\Visitor
{
    /** @var string */
    private $generatorTableName;
    /**
     * @param string $generatorTableName
     */
    public function __construct($generatorTableName = 'sequences')
    {
        $this->generatorTableName = $generatorTableName;
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSchema(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->createTable($this->generatorTableName);
        $table->addColumn('sequence_name', 'string');
        $table->addColumn('sequence_value', 'integer', ['default' => 1]);
        $table->addColumn('sequence_increment_by', 'integer', ['default' => 1]);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptColumn(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $column)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $localTable, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptIndex(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index $index)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence)
    {
    }
}
