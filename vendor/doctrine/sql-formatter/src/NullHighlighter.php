<?php

declare (strict_types=1);
namespace _PhpScoper926b1169e332\Doctrine\SqlFormatter;

final class NullHighlighter implements \_PhpScoper926b1169e332\Doctrine\SqlFormatter\Highlighter
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
