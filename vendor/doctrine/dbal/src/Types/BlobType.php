<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use function assert;
use function fopen;
use function fseek;
use function fwrite;
use function is_resource;
use function is_string;
/**
 * Type that maps an SQL BLOB to a PHP resource stream.
 */
class BlobType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getBlobTypeDeclarationSQL($column);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        if (\is_string($value)) {
            $fp = \fopen('php://temp', 'rb+');
            \assert(\is_resource($fp));
            \fwrite($fp, $value);
            \fseek($fp, 0);
            $value = $fp;
        }
        if (!\is_resource($value)) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailed($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::BLOB);
        }
        return $value;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::BLOB;
    }
    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::LARGE_OBJECT;
    }
}