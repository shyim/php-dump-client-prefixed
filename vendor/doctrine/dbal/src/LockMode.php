<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL;

/**
 * Contains all DBAL LockModes.
 */
class LockMode
{
    public const NONE = 0;
    public const OPTIMISTIC = 1;
    public const PESSIMISTIC_READ = 2;
    public const PESSIMISTIC_WRITE = 4;
    /**
     * Private constructor. This class cannot be instantiated.
     *
     * @codeCoverageIgnore
     */
    private final function __construct()
    {
    }
}
