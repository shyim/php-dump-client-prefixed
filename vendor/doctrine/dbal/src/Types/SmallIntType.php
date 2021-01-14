<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Type that maps a database SMALLINT to a PHP integer.
 */
class SmallIntType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\PhpIntegerMappingType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::SMALLINT;
    }
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getSmallIntTypeDeclarationSQL($column);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $value === null ? null : (int) $value;
    }
    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::INTEGER;
    }
}
