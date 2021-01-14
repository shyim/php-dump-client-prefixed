<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Type that maps an SQL INT to a PHP integer.
 */
class IntegerType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\PhpIntegerMappingType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::INTEGER;
    }
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
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
