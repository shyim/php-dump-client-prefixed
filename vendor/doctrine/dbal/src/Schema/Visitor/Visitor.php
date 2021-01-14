<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
/**
 * Schema Visitor used for Validation or Generation purposes.
 */
interface Visitor
{
    /**
     * @return void
     *
     * @throws SchemaException
     */
    public function acceptSchema(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema $schema);
    /**
     * @return void
     */
    public function acceptTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table);
    /**
     * @return void
     */
    public function acceptColumn(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $column);
    /**
     * @return void
     *
     * @throws SchemaException
     */
    public function acceptForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $localTable, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint);
    /**
     * @return void
     */
    public function acceptIndex(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index $index);
    /**
     * @return void
     */
    public function acceptSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence);
}
