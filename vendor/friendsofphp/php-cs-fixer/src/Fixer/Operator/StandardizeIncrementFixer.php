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

use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractIncrementOperatorFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author ntzm
 */
final class StandardizeIncrementFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractIncrementOperatorFixer
{
    /**
     * @internal
     */
    const EXPRESSION_END_TOKENS = [';', ')', ']', ',', ':', [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_CLOSE], [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_CLOSE], [\T_CLOSE_TAG]];
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Increment and decrement operators should be used if possible.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$i += 1;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$i -= 1;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before IncrementStyleFixer.
     */
    public function getPriority()
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_PLUS_EQUAL, \T_MINUS_EQUAL]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $expressionEnd = $tokens[$index];
            if (!$expressionEnd->equalsAny(self::EXPRESSION_END_TOKENS)) {
                continue;
            }
            $numberIndex = $tokens->getPrevMeaningfulToken($index);
            $number = $tokens[$numberIndex];
            if (!$number->isGivenKind(\T_LNUMBER) || '1' !== $number->getContent()) {
                continue;
            }
            $operatorIndex = $tokens->getPrevMeaningfulToken($numberIndex);
            $operator = $tokens[$operatorIndex];
            if (!$operator->isGivenKind([\T_PLUS_EQUAL, \T_MINUS_EQUAL])) {
                continue;
            }
            $startIndex = $this->findStart($tokens, $operatorIndex);
            $this->clearRangeLeaveComments($tokens, $tokens->getPrevMeaningfulToken($operatorIndex) + 1, $numberIndex);
            $tokens->insertAt($startIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token($operator->isGivenKind(\T_PLUS_EQUAL) ? [\T_INC, '++'] : [\T_DEC, '--']));
        }
    }
    /**
     * Clear tokens in the given range unless they are comments.
     *
     * @param int $indexStart
     * @param int $indexEnd
     */
    private function clearRangeLeaveComments(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $indexStart, $indexEnd)
    {
        for ($i = $indexStart; $i <= $indexEnd; ++$i) {
            $token = $tokens[$i];
            if ($token->isComment()) {
                continue;
            }
            if ($token->isWhitespace("\n\r")) {
                continue;
            }
            $tokens->clearAt($i);
        }
    }
}