<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\SQL\Parser;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types;
/**
 * Provides the behavior, features and SQL dialect of the MySQL 5.7 (5.7.9 GA) database platform.
 */
class MySQL57Platform extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQLPlatform
{
    /**
     * {@inheritdoc}
     */
    public function hasNativeJsonType()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function getJsonTypeDeclarationSQL(array $column)
    {
        return 'JSON';
    }
    public function createSQLParser() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\SQL\Parser
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\SQL\Parser(\true);
    }
    /**
     * {@inheritdoc}
     */
    protected function getPreAlterTableRenameIndexForeignKeySQL(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff $diff)
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    protected function getPostAlterTableRenameIndexForeignKeySQL(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\TableDiff $diff)
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    protected function getRenameIndexSQL($oldIndexName, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Index $index, $tableName)
    {
        return ['ALTER TABLE ' . $tableName . ' RENAME INDEX ' . $oldIndexName . ' TO ' . $index->getQuotedName($this)];
    }
    /**
     * {@inheritdoc}
     */
    protected function getReservedKeywordsClass()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords::class;
    }
    /**
     * {@inheritdoc}
     */
    protected function initializeDoctrineTypeMappings()
    {
        parent::initializeDoctrineTypeMappings();
        $this->doctrineTypeMapping['json'] = \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::JSON;
    }
}
