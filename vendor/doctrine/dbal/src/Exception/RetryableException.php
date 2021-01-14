<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;

use Throwable;
/**
 * Marker interface for all exceptions where retrying the transaction makes sense.
 *
 * @psalm-immutable
 */
interface RetryableException extends \Throwable
{
}
