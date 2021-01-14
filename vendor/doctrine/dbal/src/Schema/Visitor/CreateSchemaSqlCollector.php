<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use function array_merge;
class CreateSchemaSqlCollector extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\AbstractVisitor
{
    /** @var string[] */
    private $createNamespaceQueries = [];
    /** @var string[] */
    private $createTableQueries = [];
    /** @var string[] */
    private $createSequenceQueries = [];
    /** @var string[] */
    private $createFkConstraintQueries = [];
    /** @var AbstractPlatform */
    private $platform;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }
    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName)
    {
        if (!$this->platform->supportsSchemas()) {
            return;
        }
        $this->createNamespaceQueries[] = $this->platform->getCreateSchemaSQL($namespaceName);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table)
    {
        $this->createTableQueries = \array_merge($this->createTableQueries, $this->platform->getCreateTableSQL($table));
    }
    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $localTable, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
        if (!$this->platform->supportsForeignKeyConstraints()) {
            return;
        }
        $this->createFkConstraintQueries[] = $this->platform->getCreateForeignKeySQL($fkConstraint, $localTable);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence)
    {
        $this->createSequenceQueries[] = $this->platform->getCreateSequenceSQL($sequence);
    }
    /**
     * @return void
     */
    public function resetQueries()
    {
        $this->createNamespaceQueries = [];
        $this->createTableQueries = [];
        $this->createSequenceQueries = [];
        $this->createFkConstraintQueries = [];
    }
    /**
     * Gets all queries collected so far.
     *
     * @return string[]
     */
    public function getQueries()
    {
        return \array_merge($this->createNamespaceQueries, $this->createTableQueries, $this->createSequenceQueries, $this->createFkConstraintQueries);
    }
}
