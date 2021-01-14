<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Types;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use function array_map;
use function get_class;
/**
 * The base class for so-called Doctrine mapping types.
 *
 * A Type object is obtained by calling the static {@link getType()} method.
 */
abstract class Type
{
    /**
     * The map of supported doctrine mapping types.
     */
    private const BUILTIN_TYPES_MAP = [\_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::ARRAY => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ArrayType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::ASCII_STRING => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\AsciiStringType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::BIGINT => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\BigIntType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::BINARY => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\BinaryType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::BLOB => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\BlobType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::BOOLEAN => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\BooleanType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATE_MUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATE_IMMUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateImmutableType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATEINTERVAL => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateIntervalType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIME_MUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateTimeType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateTimeImmutableType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIMETZ_MUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateTimeTzType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DATETIMETZ_IMMUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DateTimeTzImmutableType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::DECIMAL => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\DecimalType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::FLOAT => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\FloatType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::GUID => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\GuidType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::INTEGER => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\IntegerType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::JSON => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\JsonType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::OBJECT => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\ObjectType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::SIMPLE_ARRAY => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\SimpleArrayType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::SMALLINT => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\SmallIntType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::STRING => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\StringType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::TEXT => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\TextType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::TIME_MUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\TimeType::class, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Types::TIME_IMMUTABLE => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\TimeImmutableType::class];
    /** @var TypeRegistry|null */
    private static $typeRegistry;
    /**
     * @internal Do not instantiate directly - use {@see Type::addType()} method instead.
     */
    public final function __construct()
    {
    }
    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $value;
    }
    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $value;
    }
    /**
     * Gets the SQL declaration snippet for a column of this type.
     *
     * @param mixed[]          $column   The column definition
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public abstract function getSQLDeclaration(array $column, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform);
    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    public abstract function getName();
    /**
     * @internal This method is only to be used within DBAL for forward compatibility purposes. Do not use directly.
     */
    public static final function getTypeRegistry() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\TypeRegistry
    {
        if (self::$typeRegistry === null) {
            self::$typeRegistry = self::createTypeRegistry();
        }
        return self::$typeRegistry;
    }
    private static function createTypeRegistry() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\TypeRegistry
    {
        $instances = [];
        foreach (self::BUILTIN_TYPES_MAP as $name => $class) {
            $instances[$name] = new $class();
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\TypeRegistry($instances);
    }
    /**
     * Factory method to create type instances.
     * Type instances are implemented as flyweights.
     *
     * @param string $name The name of the type (as returned by getName()).
     *
     * @return Type
     *
     * @throws Exception
     */
    public static function getType($name)
    {
        return self::getTypeRegistry()->get($name);
    }
    /**
     * Adds a custom type to the type map.
     *
     * @param string             $name      The name of the type. This should correspond to what getName() returns.
     * @param class-string<Type> $className The class name of the custom type.
     *
     * @return void
     *
     * @throws Exception
     */
    public static function addType($name, $className)
    {
        self::getTypeRegistry()->register($name, new $className());
    }
    /**
     * Checks if exists support for a type.
     *
     * @param string $name The name of the type.
     *
     * @return bool TRUE if type is supported; FALSE otherwise.
     */
    public static function hasType($name)
    {
        return self::getTypeRegistry()->has($name);
    }
    /**
     * Overrides an already defined type to use a different implementation.
     *
     * @param string             $name
     * @param class-string<Type> $className
     *
     * @return void
     *
     * @throws Exception
     */
    public static function overrideType($name, $className)
    {
        self::getTypeRegistry()->override($name, new $className());
    }
    /**
     * Gets the (preferred) binding type for values of this type that
     * can be used when binding parameters to prepared statements.
     *
     * This method should return one of the {@link ParameterType} constants.
     *
     * @return int
     */
    public function getBindingType()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING;
    }
    /**
     * Gets the types array map which holds all registered types and the corresponding
     * type class
     *
     * @return string[]
     */
    public static function getTypesMap()
    {
        return \array_map(static function (\_PhpScoper3fe455fa007d\Doctrine\DBAL\Types\Type $type) : string {
            return \get_class($type);
        }, self::getTypeRegistry()->getMap());
    }
    /**
     * Does working with this column require SQL conversion functions?
     *
     * This is a metadata function that is required for example in the ORM.
     * Usage of {@link convertToDatabaseValueSQL} and
     * {@link convertToPHPValueSQL} works for any type and mostly
     * does nothing. This method can additionally be used for optimization purposes.
     *
     * @return bool
     */
    public function canRequireSQLConversion()
    {
        return \false;
    }
    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param string $sqlExpr
     *
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $sqlExpr;
    }
    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     *
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return $sqlExpr;
    }
    /**
     * Gets an array of database types that map to this Doctrine type.
     *
     * @return string[]
     */
    public function getMappedDatabaseTypes(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return [];
    }
    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't tell them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @return bool
     */
    public function requiresSQLCommentHint(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return \false;
    }
}
