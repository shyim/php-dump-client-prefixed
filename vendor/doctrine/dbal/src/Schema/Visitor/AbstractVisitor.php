<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
/**
 * Abstract Visitor with empty methods for easy extension.
 */
class AbstractVisitor implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\Visitor, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\NamespaceVisitor
{
    public function acceptSchema(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName)
    {
    }
    public function acceptTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table)
    {
    }
    public function acceptColumn(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $column)
    {
    }
    public function acceptForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $localTable, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
    }
    public function acceptIndex(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index $index)
    {
    }
    public function acceptSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence)
    {
    }
}
