<?php

declare (strict_types=1);
namespace _PhpScopereaa8bfd44f12\Doctrine\SqlFormatter;

use function sprintf;
use const PHP_EOL;
final class CliHighlighter implements \_PhpScopereaa8bfd44f12\Doctrine\SqlFormatter\Highlighter
{
    public const HIGHLIGHT_FUNCTIONS = 'functions';
    /** @var array<string, string> */
    private $escapeSequences;
    /**
     * @param array<string, string> $escapeSequences
     */
    public function __construct(array $escapeSequences = [])
    {
        $this->escapeSequences = $escapeSequences + [self::HIGHLIGHT_QUOTE => "\33[34;1m", self::HIGHLIGHT_BACKTICK_QUOTE => "\33[35;1m", self::HIGHLIGHT_RESERVED => "\33[37m", self::HIGHLIGHT_BOUNDARY => '', self::HIGHLIGHT_NUMBER => "\33[32;1m", self::HIGHLIGHT_WORD => '', self::HIGHLIGHT_ERROR => "\33[31;1;7m", self::HIGHLIGHT_COMMENT => "\33[30;1m", self::HIGHLIGHT_VARIABLE => "\33[36;1m", self::HIGHLIGHT_FUNCTIONS => "\33[37m"];
    }
    public function highlightToken(int $type, string $value) : string
    {
        if ($type === \_PhpScopereaa8bfd44f12\Doctrine\SqlFormatter\Token::TOKEN_TYPE_BOUNDARY && ($value === '(' || $value === ')')) {
            return $value;
        }
        $prefix = $this->prefix($type);
        if ($prefix === null) {
            return $value;
        }
        return $prefix . $value . "\33[0m";
    }
    private function prefix(int $type) : ?string
    {
        if (!isset(self::TOKEN_TYPE_TO_HIGHLIGHT[$type])) {
            return null;
        }
        return $this->escapeSequences[self::TOKEN_TYPE_TO_HIGHLIGHT[$type]];
    }
    public function highlightError(string $value) : string
    {
        return \sprintf('%s%s%s%s', \PHP_EOL, $this->escapeSequences[self::HIGHLIGHT_ERROR], $value, "\33[0m");
    }
    public function highlightErrorMessage(string $value) : string
    {
        return $this->highlightError($value);
    }
    public function output(string $string) : string
    {
        return $string . "\n";
    }
}
