<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use DateTime;
use DateTimeInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * DateTime type saving additional timezone information.
 *
 * Caution: Databases are not necessarily experts at storing timezone related
 * data of dates. First, of all the supported vendors only PostgreSQL and Oracle
 * support storing Timezone data. But those two don't save the actual timezone
 * attached to a DateTime instance (for example "Europe/Berlin" or "America/Montreal")
 * but the current offset of them related to UTC. That means depending on daylight saving times
 * or not you may get different offsets.
 *
 * This datatype makes only sense to use, if your application works with an offset, not
 * with an actual timezone that uses transitions. Otherwise your DateTime instance
 * attached with a timezone such as Europe/Berlin gets saved into the database with
 * the offset and re-created from persistence with only the offset, not the original timezone
 * attached.
 */
class DateTimeTzType extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\PhpDateTimeMappingType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIMETZ_MUTABLE;
    }
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getDateTimeTzTypeDeclarationSQL($column);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format($platform->getDateTimeTzFormatString());
        }
        throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }
        $val = \DateTime::createFromFormat($platform->getDateTimeTzFormatString(), $value);
        if ($val === \false) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
        }
        return $val;
    }
}
