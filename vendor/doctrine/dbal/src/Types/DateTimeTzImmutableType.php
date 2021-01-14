<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use DateTimeImmutable;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Immutable type of {@see DateTimeTzType}.
 */
class DateTimeTzImmutableType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateTimeTzType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIMETZ_IMMUTABLE;
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
            return $value->format($platform->getDateTimeTzFormatString());
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
        $dateTime = \DateTimeImmutable::createFromFormat($platform->getDateTimeTzFormatString(), $value);
        if ($dateTime === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
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
