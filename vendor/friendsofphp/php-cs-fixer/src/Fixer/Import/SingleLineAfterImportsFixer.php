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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Import;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Utils;
/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Ceeram <ceeram@cakephp.org>
 * @author Graham Campbell <graham@alt-three.com>
 */
final class SingleLineAfterImportsFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_USE);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
namespace Foo;

use Bar;
use Baz;
final class Example
{
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
namespace Foo;

use Bar;
use Baz;


final class Example
{
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after NoUnusedImportsFixer.
     */
    public function getPriority()
    {
        return -11;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $ending = $this->whitespacesConfig->getLineEnding();
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $added = 0;
        foreach ($tokensAnalyzer->getImportUseIndexes() as $index) {
            $index += $added;
            $indent = '';
            // if previous line ends with comment and current line starts with whitespace, use current indent
            if ($tokens[$index - 1]->isWhitespace(" \t") && $tokens[$index - 2]->isGivenKind(\T_COMMENT)) {
                $indent = $tokens[$index - 1]->getContent();
            } elseif ($tokens[$index - 1]->isWhitespace()) {
                $indent = \_PhpScoper3fe455fa007d\PhpCsFixer\Utils::calculateTrailingWhitespaceIndent($tokens[$index - 1]);
            }
            $semicolonIndex = $tokens->getNextTokenOfKind($index, [';', [\T_CLOSE_TAG]]);
            // Handle insert index for inline T_COMMENT with whitespace after semicolon
            $insertIndex = $semicolonIndex;
            if ($tokens[$semicolonIndex]->isGivenKind(\T_CLOSE_TAG)) {
                if ($tokens[$insertIndex - 1]->isWhitespace()) {
                    --$insertIndex;
                }
                $tokens->insertAt($insertIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(';'));
                ++$added;
            }
            if ($semicolonIndex === \count($tokens) - 1) {
                $tokens->insertAt($insertIndex + 1, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $ending . $ending . $indent]));
                ++$added;
            } else {
                $newline = $ending;
                $tokens[$semicolonIndex]->isGivenKind(\T_CLOSE_TAG) ? --$insertIndex : ++$insertIndex;
                if ($tokens[$insertIndex]->isWhitespace(" \t") && $tokens[$insertIndex + 1]->isComment()) {
                    ++$insertIndex;
                }
                // Increment insert index for inline T_COMMENT or T_DOC_COMMENT
                if ($tokens[$insertIndex]->isComment()) {
                    ++$insertIndex;
                }
                $afterSemicolon = $tokens->getNextMeaningfulToken($semicolonIndex);
                if (null === $afterSemicolon || !$tokens[$afterSemicolon]->isGivenKind(\T_USE)) {
                    $newline .= $ending;
                }
                if ($tokens[$insertIndex]->isWhitespace()) {
                    $nextToken = $tokens[$insertIndex];
                    if (2 === \substr_count($nextToken->getContent(), "\n")) {
                        continue;
                    }
                    $nextMeaningfulAfterUseIndex = $tokens->getNextMeaningfulToken($insertIndex);
                    if (null !== $nextMeaningfulAfterUseIndex && $tokens[$nextMeaningfulAfterUseIndex]->isGivenKind(\T_USE)) {
                        if (\substr_count($nextToken->getContent(), "\n") < 1) {
                            $tokens[$insertIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $newline . $indent . \ltrim($nextToken->getContent())]);
                        }
                    } else {
                        $tokens[$insertIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $newline . $indent . \ltrim($nextToken->getContent())]);
                    }
                } else {
                    $tokens->insertAt($insertIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $newline . $indent]));
                    ++$added;
                }
            }
        }
    }
}
