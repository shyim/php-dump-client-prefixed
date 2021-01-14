<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\InvalidOption;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer;
use mysqli;
use function mysqli_options;
final class Options implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer
{
    /** @var array<int,mixed> */
    private $options;
    /**
     * @param array<int,mixed> $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }
    public function initialize(\mysqli $connection) : void
    {
        foreach ($this->options as $option => $value) {
            if (!\mysqli_options($connection, $option, $value)) {
                throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\InvalidOption::fromOption($option, $value);
            }
        }
    }
}
