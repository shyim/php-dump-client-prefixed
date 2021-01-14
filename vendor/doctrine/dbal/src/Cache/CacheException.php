<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Cache;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
/**
 * @psalm-immutable
 */
class CacheException extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception
{
    /**
     * @return CacheException
     */
    public static function noCacheKey()
    {
        return new self('No cache key was set.');
    }
    /**
     * @return CacheException
     */
    public static function noResultDriverConfigured()
    {
        return new self('Trying to cache a query but no result driver is configured.');
    }
}
