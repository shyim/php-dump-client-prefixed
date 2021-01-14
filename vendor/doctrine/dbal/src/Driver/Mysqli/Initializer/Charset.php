<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\InvalidCharset;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer;
use mysqli;
final class Charset implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer
{
    /** @var string */
    private $charset;
    public function __construct(string $charset)
    {
        $this->charset = $charset;
    }
    public function initialize(\mysqli $connection) : void
    {
        if ($connection->set_charset($this->charset)) {
            return;
        }
        throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\InvalidCharset::fromCharset($connection, $this->charset);
    }
}
