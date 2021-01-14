<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\Visitor;
use function count;
use function implode;
use function str_replace;
class ReservedKeywordsValidator implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor\Visitor
{
    /** @var KeywordList[] */
    private $keywordLists = [];
    /** @var string[] */
    private $violations = [];
    /**
     * @param KeywordList[] $keywordLists
     */
    public function __construct(array $keywordLists)
    {
        $this->keywordLists = $keywordLists;
    }
    /**
     * @return string[]
     */
    public function getViolations()
    {
        return $this->violations;
    }
    /**
     * @param string $word
     *
     * @return string[]
     */
    private function isReservedWord($word)
    {
        if ($word[0] === '`') {
            $word = \str_replace('`', '', $word);
        }
        $keywordLists = [];
        foreach ($this->keywordLists as $keywordList) {
            if (!$keywordList->isKeyword($word)) {
                continue;
            }
            $keywordLists[] = $keywordList->getName();
        }
        return $keywordLists;
    }
    /**
     * @param string   $asset
     * @param string[] $violatedPlatforms
     *
     * @return void
     */
    private function addViolation($asset, $violatedPlatforms)
    {
        if (\count($violatedPlatforms) === 0) {
            return;
        }
        $this->violations[] = $asset . ' keyword violations: ' . \implode(', ', $violatedPlatforms);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptColumn(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Column $column)
    {
        $this->addViolation('Table ' . $table->getName() . ' column ' . $column->getName(), $this->isReservedWord($column->getName()));
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
    public function acceptSchema(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Sequence $sequence)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Table $table)
    {
        $this->addViolation('Table ' . $table->getName(), $this->isReservedWord($table->getName()));
    }
}
