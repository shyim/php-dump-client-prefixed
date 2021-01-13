<?php

namespace _PhpScoper5aadddf2c2bd;

#[Attribute(Attribute::TARGET_CLASS)]
final class Attribute
{
    public const TARGET_CLASS = 1;
    public const TARGET_FUNCTION = 2;
    public const TARGET_METHOD = 4;
    public const TARGET_PROPERTY = 8;
    public const TARGET_CLASS_CONSTANT = 16;
    public const TARGET_PARAMETER = 32;
    public const TARGET_ALL = 63;
    public const IS_REPEATABLE = 64;
    /** @var int */
    public $flags;
    public function __construct(int $flags = self::TARGET_ALL)
    {
        $this->flags = $flags;
    }
}
#[Attribute(Attribute::TARGET_CLASS)]
\class_alias('_PhpScoper5aadddf2c2bd\\Attribute', 'Attribute', \false);
