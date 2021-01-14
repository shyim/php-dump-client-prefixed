<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use mysqli;
interface Initializer
{
    /**
     * @throws Exception
     */
    public function initialize(\mysqli $connection) : void;
}
