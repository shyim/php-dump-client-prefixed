<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Portability;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver as DriverInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Middleware as MiddlewareInterface;
final class Middleware implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Middleware
{
    /** @var int */
    private $mode;
    /** @var int */
    private $case;
    public function __construct(int $mode, int $case)
    {
        $this->mode = $mode;
        $this->case = $case;
    }
    public function wrap(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver $driver) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver
    {
        if ($this->mode !== 0) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Driver($driver, $this->mode, $this->case);
        }
        return $driver;
    }
}
