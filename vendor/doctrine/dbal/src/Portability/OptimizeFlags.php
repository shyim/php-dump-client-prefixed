<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Portability;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\DB2Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\OraclePlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SQLServer2012Platform;
final class OptimizeFlags
{
    /**
     * Platform-specific portability flags that need to be excluded from the user-provided mode
     * since the platform already operates in this mode to avoid unnecessary conversion overhead.
     *
     * @var array<string,int>
     */
    private static $platforms = [\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\DB2Platform::class => 0, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\OraclePlatform::class => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Connection::PORTABILITY_EMPTY_TO_NULL, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\PostgreSQL94Platform::class => 0, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform::class => 0, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SQLServer2012Platform::class => 0];
    public function __invoke(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform, int $flags) : int
    {
        foreach (self::$platforms as $class => $mask) {
            if ($platform instanceof $class) {
                $flags &= ~$mask;
                break;
            }
        }
        return $flags;
    }
}
