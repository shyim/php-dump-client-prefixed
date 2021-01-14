<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use function count;
use function explode;
use function implode;
use function is_array;
use function is_resource;
use function stream_get_contents;
/**
 * Array Type which can be used for simple values.
 *
 * Only use this type if you are sure that your values cannot contain a ",".
 */
class SimpleArrayType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if (!\is_array($value) || \count($value) === 0) {
            return null;
        }
        return \implode(',', $value);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null) {
            return [];
        }
        $value = \is_resource($value) ? \stream_get_contents($value) : $value;
        return \explode(',', $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::SIMPLE_ARRAY;
    }
    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return \true;
    }
}
