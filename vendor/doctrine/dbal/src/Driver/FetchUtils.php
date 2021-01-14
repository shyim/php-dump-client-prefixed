<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

/**
 * @internal
 */
final class FetchUtils
{
    /**
     * @return mixed|false
     *
     * @throws Exception
     */
    public static function fetchOne(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result $result)
    {
        $row = $result->fetchNumeric();
        if ($row === \false) {
            return \false;
        }
        return $row[0];
    }
    /**
     * @return list<list<mixed>>
     *
     * @throws Exception
     */
    public static function fetchAllNumeric(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result $result) : array
    {
        $rows = [];
        while (($row = $result->fetchNumeric()) !== \false) {
            $rows[] = $row;
        }
        return $rows;
    }
    /**
     * @return list<array<string,mixed>>
     *
     * @throws Exception
     */
    public static function fetchAllAssociative(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result $result) : array
    {
        $rows = [];
        while (($row = $result->fetchAssociative()) !== \false) {
            $rows[] = $row;
        }
        return $rows;
    }
    /**
     * @return list<mixed>
     *
     * @throws Exception
     */
    public static function fetchFirstColumn(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result $result) : array
    {
        $rows = [];
        while (($row = $result->fetchOne()) !== \false) {
            $rows[] = $row;
        }
        return $rows;
    }
}
