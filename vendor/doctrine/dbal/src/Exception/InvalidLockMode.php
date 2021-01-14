<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use function sprintf;
/**
 * @psalm-immutable
 */
class InvalidLockMode extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception
{
    public static function fromLockMode(int $lockMode) : self
    {
        return new self(\sprintf('Lock mode %d is invalid. The valid values are LockMode::NONE, LockMode::OPTIMISTIC' . ', LockMode::PESSIMISTIC_READ and LockMode::PESSIMISTIC_WRITE', $lockMode));
    }
}
