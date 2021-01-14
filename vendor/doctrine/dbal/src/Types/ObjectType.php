<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use function is_resource;
use function restore_error_handler;
use function serialize;
use function set_error_handler;
use function stream_get_contents;
use function unserialize;
/**
 * Type that maps a PHP object to a clob SQL type.
 */
class ObjectType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type
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
        return \serialize($value);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        $value = \is_resource($value) ? \stream_get_contents($value) : $value;
        \set_error_handler(function (int $code, string $message) : bool {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailedUnserialization($this->getName(), $message);
        });
        try {
            return \unserialize($value);
        } finally {
            \restore_error_handler();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::OBJECT;
    }
    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return \true;
    }
}
