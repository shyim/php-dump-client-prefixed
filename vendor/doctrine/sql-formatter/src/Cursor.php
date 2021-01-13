<?php

declare (strict_types=1);
namespace _PhpScopereaa8bfd44f12\Doctrine\SqlFormatter;

final class Cursor
{
    /** @var int */
    private $position = -1;
    /** @var Token[] */
    private $tokens;
    /**
     * @param Token[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }
    public function next(?int $exceptTokenType = null) : ?\_PhpScopereaa8bfd44f12\Doctrine\SqlFormatter\Token
    {
        while ($token = $this->tokens[++$this->position] ?? null) {
            if ($exceptTokenType !== null && $token->isOfType($exceptTokenType)) {
                continue;
            }
            return $token;
        }
        return null;
    }
    public function previous(?int $exceptTokenType = null) : ?\_PhpScopereaa8bfd44f12\Doctrine\SqlFormatter\Token
    {
        while ($token = $this->tokens[--$this->position] ?? null) {
            if ($exceptTokenType !== null && $token->isOfType($exceptTokenType)) {
                continue;
            }
            return $token;
        }
        return null;
    }
    public function subCursor() : self
    {
        $cursor = new self($this->tokens);
        $cursor->position = $this->position;
        return $cursor;
    }
}
