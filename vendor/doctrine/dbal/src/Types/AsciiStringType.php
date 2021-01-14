<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
final class AsciiStringType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\StringType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getAsciiStringTypeDeclarationSQL($column);
    }
    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::ASCII;
    }
    public function getName() : string
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::ASCII_STRING;
    }
}
