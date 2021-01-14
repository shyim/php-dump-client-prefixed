<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff;
/**
 * Visit a SchemaDiff.
 */
interface SchemaDiffVisitor
{
    /**
     * Visit an orphaned foreign key whose table was deleted.
     *
     * @return void
     */
    public function visitOrphanedForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey);
    /**
     * Visit a sequence that has changed.
     *
     * @return void
     */
    public function visitChangedSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence);
    /**
     * Visit a sequence that has been removed.
     *
     * @return void
     */
    public function visitRemovedSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence);
    /** @return void */
    public function visitNewSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence);
    /** @return void */
    public function visitNewTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table);
    /** @return void */
    public function visitNewTableForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey);
    /** @return void */
    public function visitRemovedTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table);
    /** @return void */
    public function visitChangedTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff $tableDiff);
}
