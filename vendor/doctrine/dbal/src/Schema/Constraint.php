<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Marker interface for constraints.
 */
interface Constraint
{
    /**
     * @return string
     */
    public function getName();
    /**
     * @return string
     */
    public function getQuotedName(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform);
    /**
     * Returns the names of the referencing table columns
     * the constraint is associated with.
     *
     * @return string[]
     */
    public function getColumns();
    /**
     * Returns the quoted representation of the column names
     * the constraint is associated with.
     *
     * But only if they were defined with one or a column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param AbstractPlatform $platform The platform to use for quotation.
     *
     * @return string[]
     */
    public function getQuotedColumns(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform);
}