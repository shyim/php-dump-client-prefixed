<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use SplObjectStorage;
use function assert;
use function strlen;
/**
 * Gathers SQL statements that allow to completely drop the current schema.
 */
class DropSchemaSqlCollector extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\AbstractVisitor
{
    /** @var SplObjectStorage */
    private $constraints;
    /** @var SplObjectStorage */
    private $sequences;
    /** @var SplObjectStorage */
    private $tables;
    /** @var AbstractPlatform */
    private $platform;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->platform = $platform;
        $this->clearQueries();
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table)
    {
        $this->tables->attach($table);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $localTable, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
        if (\strlen($fkConstraint->getName()) === 0) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException::namedForeignKeyRequired($localTable, $fkConstraint);
        }
        $this->constraints->attach($fkConstraint, $localTable);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence)
    {
        $this->sequences->attach($sequence);
    }
    /**
     * @return void
     */
    public function clearQueries()
    {
        $this->constraints = new \SplObjectStorage();
        $this->sequences = new \SplObjectStorage();
        $this->tables = new \SplObjectStorage();
    }
    /**
     * @return string[]
     */
    public function getQueries()
    {
        $sql = [];
        foreach ($this->constraints as $fkConstraint) {
            \assert($fkConstraint instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint);
            $localTable = $this->constraints[$fkConstraint];
            $sql[] = $this->platform->getDropForeignKeySQL($fkConstraint, $localTable);
        }
        foreach ($this->sequences as $sequence) {
            \assert($sequence instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence);
            $sql[] = $this->platform->getDropSequenceSQL($sequence);
        }
        foreach ($this->tables as $table) {
            \assert($table instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table);
            $sql[] = $this->platform->getDropTableSQL($table);
        }
        return $sql;
    }
}
