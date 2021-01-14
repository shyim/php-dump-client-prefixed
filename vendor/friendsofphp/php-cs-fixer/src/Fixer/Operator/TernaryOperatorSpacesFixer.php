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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Operator;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\GotoLabelAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class TernaryOperatorSpacesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Standardize spaces around ternary operator.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php \$a = \$a   ?1 :0;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after ArraySyntaxFixer, ListSyntaxFixer, TernaryToElvisOperatorFixer.
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(['?', ':']);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $gotoLabelAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\GotoLabelAnalyzer();
        $ternaryOperatorIndices = [];
        $excludedIndices = [];
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(\T_SWITCH)) {
                $excludedIndices = \array_merge($excludedIndices, $this->getColonIndicesForSwitch($tokens, $index));
                continue;
            }
            if (!$token->equalsAny(['?', ':'])) {
                continue;
            }
            if (\in_array($index, $excludedIndices, \true)) {
                continue;
            }
            if ($this->belongsToAlternativeSyntax($tokens, $index)) {
                continue;
            }
            if ($gotoLabelAnalyzer->belongsToGoToLabel($tokens, $index)) {
                continue;
            }
            $ternaryOperatorIndices[] = $index;
        }
        foreach (\array_reverse($ternaryOperatorIndices) as $index) {
            $token = $tokens[$index];
            if ($token->equals('?')) {
                $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);
                if ($tokens[$nextNonWhitespaceIndex]->equals(':')) {
                    // for `$a ?: $b` remove spaces between `?` and `:`
                    $tokens->ensureWhitespaceAtIndex($index + 1, 0, '');
                } else {
                    // for `$a ? $b : $c` ensure space after `?`
                    $this->ensureWhitespaceExistence($tokens, $index + 1, \true);
                }
                // for `$a ? $b : $c` ensure space before `?`
                $this->ensureWhitespaceExistence($tokens, $index - 1, \false);
                continue;
            }
            if ($token->equals(':')) {
                // for `$a ? $b : $c` ensure space after `:`
                $this->ensureWhitespaceExistence($tokens, $index + 1, \true);
                $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];
                if (!$prevNonWhitespaceToken->equals('?')) {
                    // for `$a ? $b : $c` ensure space before `:`
                    $this->ensureWhitespaceExistence($tokens, $index - 1, \false);
                }
            }
        }
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function belongsToAlternativeSyntax(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if (!$tokens[$index]->equals(':')) {
            return \false;
        }
        $closeParenthesisIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$closeParenthesisIndex]->isGivenKind(\T_ELSE)) {
            return \true;
        }
        if (!$tokens[$closeParenthesisIndex]->equals(')')) {
            return \false;
        }
        $openParenthesisIndex = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $closeParenthesisIndex);
        $alternativeControlStructureIndex = $tokens->getPrevMeaningfulToken($openParenthesisIndex);
        return $tokens[$alternativeControlStructureIndex]->isGivenKind([\T_DECLARE, \T_ELSEIF, \T_FOR, \T_FOREACH, \T_IF, \T_SWITCH, \T_WHILE]);
    }
    /**
     * @param int $switchIndex
     *
     * @return int[]
     */
    private function getColonIndicesForSwitch(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $switchIndex)
    {
        return \array_map(static function (\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis $caseAnalysis) {
            return $caseAnalysis->getColonIndex();
        }, (new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer())->getSwitchAnalysis($tokens, $switchIndex)->getCases());
    }
    /**
     * @param int  $index
     * @param bool $after
     */
    private function ensureWhitespaceExistence(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $after)
    {
        if ($tokens[$index]->isWhitespace()) {
            if (\false === \strpos($tokens[$index]->getContent(), "\n") && !$tokens[$index - 1]->isComment()) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
            }
            return;
        }
        $index += $after ? 0 : 1;
        $tokens->insertAt($index, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
    }
}
