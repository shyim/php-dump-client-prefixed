<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms;

/**
 * Provides the behavior, features and SQL dialect of the MySQL 8.0 (8.0 GA) database platform.
 */
class MySQL80Platform extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\MySQL57Platform
{
    /**
     * {@inheritdoc}
     */
    protected function getReservedKeywordsClass()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQL80Keywords::class;
    }
}
