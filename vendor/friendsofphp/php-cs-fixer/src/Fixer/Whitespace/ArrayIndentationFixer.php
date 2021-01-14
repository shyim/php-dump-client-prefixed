<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Whitespace;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
final class ArrayIndentationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Each element of an array must be indented exactly once.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$foo = [\n   'bar' => [\n    'baz' => true,\n  ],\n];\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_ARRAY, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before AlignMultilineCommentFixer, BinaryOperatorSpacesFixer.
     * Must run after BracesFixer, MethodArgumentSpaceFixer, MethodChainingIndentationFixer.
     */
    public function getPriority()
    {
        return -31;
    }
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $scopes = [];
        $previousLineInitialIndent = '';
        $previousLineNewIndent = '';
        foreach ($tokens as $index => $token) {
            $currentScope = [] !== $scopes ? \count($scopes) - 1 : null;
            if ($token->isComment()) {
                continue;
            }
            if ($token->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN) || $token->equals('(') && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(\T_ARRAY)) {
                $endIndex = $tokens->findBlockEnd($token->equals('(') ? \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE : \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                $scopes[] = ['type' => 'array', 'end_index' => $endIndex, 'initial_indent' => $this->getLineIndentation($tokens, $index)];
                continue;
            }
            if (null === $currentScope) {
                continue;
            }
            if ($token->isWhitespace()) {
                if (!\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $token->getContent())) {
                    continue;
                }
                if ('array' === $scopes[$currentScope]['type']) {
                    $indent = \false;
                    for ($searchEndIndex = $index + 1; $searchEndIndex < $scopes[$currentScope]['end_index']; ++$searchEndIndex) {
                        $searchEndToken = $tokens[$searchEndIndex];
                        if (!$searchEndToken->isWhitespace() && !$searchEndToken->isComment() || $searchEndToken->isWhitespace() && \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $searchEndToken->getContent())) {
                            $indent = \true;
                            break;
                        }
                    }
                    $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(\\R+)\\h*$/', '$1' . $scopes[$currentScope]['initial_indent'] . ($indent ? $this->whitespacesConfig->getIndent() : ''), $token->getContent());
                    $previousLineInitialIndent = $this->extractIndent($token->getContent());
                    $previousLineNewIndent = $this->extractIndent($content);
                } else {
                    $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(\\R)' . \preg_quote($scopes[$currentScope]['initial_indent'], '/') . '(\\h*)$/', '$1' . $scopes[$currentScope]['new_indent'] . '$2', $token->getContent());
                }
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]);
                continue;
            }
            if ($index === $scopes[$currentScope]['end_index']) {
                while ([] !== $scopes && $index === $scopes[$currentScope]['end_index']) {
                    \array_pop($scopes);
                    --$currentScope;
                }
                continue;
            }
            if ($token->equals(',')) {
                continue;
            }
            if ('expression' !== $scopes[$currentScope]['type']) {
                $endIndex = $this->findExpressionEndIndex($tokens, $index, $scopes[$currentScope]['end_index']);
                if ($endIndex === $index) {
                    continue;
                }
                $scopes[] = ['type' => 'expression', 'end_index' => $endIndex, 'initial_indent' => $previousLineInitialIndent, 'new_indent' => $previousLineNewIndent];
            }
        }
    }
    private function findExpressionEndIndex(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $parentScopeEndIndex)
    {
        $endIndex = null;
        for ($searchEndIndex = $index + 1; $searchEndIndex < $parentScopeEndIndex; ++$searchEndIndex) {
            $searchEndToken = $tokens[$searchEndIndex];
            if ($searchEndToken->equalsAny(['(', '{']) || $searchEndToken->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $type = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::detectBlockType($searchEndToken);
                $searchEndIndex = $tokens->findBlockEnd($type['type'], $searchEndIndex);
                continue;
            }
            if ($searchEndToken->equals(',')) {
                $endIndex = $tokens->getPrevMeaningfulToken($searchEndIndex);
                break;
            }
        }
        if (null !== $endIndex) {
            return $endIndex;
        }
        return $tokens->getPrevMeaningfulToken($parentScopeEndIndex);
    }
    private function getLineIndentation(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $newlineTokenIndex = $this->getPreviousNewlineTokenIndex($tokens, $index);
        if (null === $newlineTokenIndex) {
            return '';
        }
        return $this->extractIndent($this->computeNewLineContent($tokens, $newlineTokenIndex));
    }
    private function extractIndent($content)
    {
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R(\\h*)[^\\r\\n]*$/D', $content, $matches)) {
            return $matches[1];
        }
        return '';
    }
    private function getPreviousNewlineTokenIndex(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        while ($index > 0) {
            $index = $tokens->getPrevTokenOfKind($index, [[\T_WHITESPACE], [\T_INLINE_HTML]]);
            if (null === $index) {
                break;
            }
            if ($this->isNewLineToken($tokens, $index)) {
                return $index;
            }
        }
        return null;
    }
    private function isNewLineToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind([\T_WHITESPACE, \T_INLINE_HTML])) {
            return \false;
        }
        return (bool) \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $this->computeNewLineContent($tokens, $index));
    }
    private function computeNewLineContent(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $content = $tokens[$index]->getContent();
        if (0 !== $index && $tokens[$index - 1]->equalsAny([[\T_OPEN_TAG], [\T_CLOSE_TAG]])) {
            $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\S/', '', $tokens[$index - 1]->getContent()) . $content;
        }
        return $content;
    }
}
