<?php

declare (strict_types=1);
namespace _PhpScoper5aadddf2c2bd\Doctrine\SqlFormatter;

final class NullHighlighter implements \_PhpScoper5aadddf2c2bd\Doctrine\SqlFormatter\Highlighter
{
    public function highlightToken(int $type, string $value) : string
    {
        return $value;
    }
    public function highlightError(string $value) : string
    {
        return $value;
    }
    public function highlightErrorMessage(string $value) : string
    {
        return ' ' . $value;
    }
    public function output(string $string) : string
    {
        return $string;
    }
}
