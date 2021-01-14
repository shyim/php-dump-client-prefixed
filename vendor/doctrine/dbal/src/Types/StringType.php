<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Type that maps an SQL VARCHAR to a PHP string.
 */
class StringType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::STRING;
    }
}
