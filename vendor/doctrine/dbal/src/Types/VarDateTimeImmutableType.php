<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use DateTimeImmutable;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use function date_create_immutable;
/**
 * Immutable type of {@see VarDateTimeType}.
 */
class VarDateTimeImmutableType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\VarDateTimeType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE;
    }
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }
        if ($value instanceof \DateTimeImmutable) {
            return $value->format($platform->getDateTimeFormatString());
        }
        throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', \DateTimeImmutable::class]);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }
        $dateTime = \date_create_immutable($value);
        if ($dateTime === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailed($value, $this->getName());
        }
        return $dateTime;
    }
    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return \true;
    }
}
