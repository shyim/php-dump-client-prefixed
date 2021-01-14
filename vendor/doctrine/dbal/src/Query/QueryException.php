<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Query;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use function implode;
/**
 * @psalm-immutable
 */
class QueryException extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception
{
    /**
     * @param string   $alias
     * @param string[] $registeredAliases
     *
     * @return QueryException
     */
    public static function unknownAlias($alias, $registeredAliases)
    {
        return new self("The given alias '" . $alias . "' is not part of " . 'any FROM or JOIN clause table. The currently registered ' . 'aliases are: ' . \implode(', ', $registeredAliases) . '.');
    }
    /**
     * @param string   $alias
     * @param string[] $registeredAliases
     *
     * @return QueryException
     */
    public static function nonUniqueAlias($alias, $registeredAliases)
    {
        return new self("The given alias '" . $alias . "' is not unique " . 'in FROM and JOIN clause table. The currently registered ' . 'aliases are: ' . \implode(', ', $registeredAliases) . '.');
    }
}
