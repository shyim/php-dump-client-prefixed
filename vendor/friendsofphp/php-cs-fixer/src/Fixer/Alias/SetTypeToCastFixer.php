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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Alias;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFunctionReferenceFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class SetTypeToCastFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFunctionReferenceFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Cast shall be used, not `settype`.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
settype($foo, "integer");
settype($bar, "string");
settype($bar, "null");
')], null, 'Risky when the `settype` function is overridden or when used as the 2nd or 3rd expression in a `for` loop .');
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_CONSTANT_ENCAPSED_STRING, \T_STRING, \T_VARIABLE]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $map = ['array' => [\T_ARRAY_CAST, '(array)'], 'bool' => [\T_BOOL_CAST, '(bool)'], 'boolean' => [\T_BOOL_CAST, '(bool)'], 'double' => [\T_DOUBLE_CAST, '(float)'], 'float' => [\T_DOUBLE_CAST, '(float)'], 'int' => [\T_INT_CAST, '(int)'], 'integer' => [\T_INT_CAST, '(int)'], 'object' => [\T_OBJECT_CAST, '(object)'], 'string' => [\T_STRING_CAST, '(string)']];
        $argumentsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        foreach (\array_reverse($this->findSettypeCalls($tokens)) as $candidate) {
            $functionNameIndex = $candidate[0];
            $arguments = $argumentsAnalyzer->getArguments($tokens, $candidate[1], $candidate[2]);
            if (2 !== \count($arguments)) {
                continue;
                // function must be overridden or used incorrectly
            }
            $prev = $tokens->getPrevMeaningfulToken($functionNameIndex);
            if (!$tokens[$prev]->isGivenKind(\T_OPEN_TAG) && !$tokens[$prev]->equalsAny([';', '{'])) {
                continue;
                // return value of the function is used
            }
            \reset($arguments);
            // --- Test first argument --------------------
            $firstArgumentStart = \key($arguments);
            if ($tokens[$firstArgumentStart]->isComment() || $tokens[$firstArgumentStart]->isWhitespace()) {
                $firstArgumentStart = $tokens->getNextMeaningfulToken($firstArgumentStart);
            }
            if (!$tokens[$firstArgumentStart]->isGivenKind(\T_VARIABLE)) {
                continue;
                // settype only works with variables pass by reference, function must be overridden
            }
            $commaIndex = $tokens->getNextMeaningfulToken($firstArgumentStart);
            if (null === $commaIndex || !$tokens[$commaIndex]->equals(',')) {
                continue;
                // first argument is complex statement; function must be overridden
            }
            // --- Test second argument -------------------
            \next($arguments);
            $secondArgumentStart = \key($arguments);
            $secondArgumentEnd = $arguments[$secondArgumentStart];
            if ($tokens[$secondArgumentStart]->isComment() || $tokens[$secondArgumentStart]->isWhitespace()) {
                $secondArgumentStart = $tokens->getNextMeaningfulToken($secondArgumentStart);
            }
            if (!$tokens[$secondArgumentStart]->isGivenKind(\T_CONSTANT_ENCAPSED_STRING) || $tokens->getNextMeaningfulToken($secondArgumentStart) < $secondArgumentEnd) {
                continue;
                // second argument is of the wrong type or is a (complex) statement of some sort (function is overridden)
            }
            // --- Test type ------------------------------
            $type = \strtolower(\trim($tokens[$secondArgumentStart]->getContent(), '"\'"'));
            if ('null' !== $type && !isset($map[$type])) {
                continue;
                // we don't know how to map
            }
            // --- Fixing ---------------------------------
            $argumentToken = $tokens[$firstArgumentStart];
            $this->removeSettypeCall($tokens, $functionNameIndex, $candidate[1], $firstArgumentStart, $commaIndex, $secondArgumentStart, $candidate[2]);
            if ('null' === $type) {
                $this->findSettypeNullCall($tokens, $functionNameIndex, $argumentToken);
            } else {
                $this->fixSettypeCall($tokens, $functionNameIndex, $argumentToken, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token($map[$type]));
            }
        }
    }
    private function findSettypeCalls(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $candidates = [];
        $end = \count($tokens);
        for ($i = 1; $i < $end; ++$i) {
            $candidate = $this->find('settype', $tokens, $i, $end);
            if (null === $candidate) {
                break;
            }
            $i = $candidate[1];
            // proceed to openParenthesisIndex
            $candidates[] = $candidate;
        }
        return $candidates;
    }
    /**
     * @param int $functionNameIndex
     * @param int $openParenthesisIndex
     * @param int $firstArgumentStart
     * @param int $commaIndex
     * @param int $secondArgumentStart
     * @param int $closeParenthesisIndex
     */
    private function removeSettypeCall(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $functionNameIndex, $openParenthesisIndex, $firstArgumentStart, $commaIndex, $secondArgumentStart, $closeParenthesisIndex)
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($closeParenthesisIndex);
        $prevIndex = $tokens->getPrevMeaningfulToken($closeParenthesisIndex);
        if ($tokens[$prevIndex]->equals(',')) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($prevIndex);
        }
        $tokens->clearTokenAndMergeSurroundingWhitespace($secondArgumentStart);
        $tokens->clearTokenAndMergeSurroundingWhitespace($commaIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($firstArgumentStart);
        $tokens->clearTokenAndMergeSurroundingWhitespace($openParenthesisIndex);
        $tokens->clearAt($functionNameIndex);
        // we'll be inserting here so no need to merge the space tokens
        $tokens->clearEmptyTokens();
    }
    /**
     * @param int $functionNameIndex
     */
    private function fixSettypeCall(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $functionNameIndex, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $argumentToken, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $castToken)
    {
        $tokens->insertAt($functionNameIndex, [clone $argumentToken, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('='), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), $castToken, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), clone $argumentToken]);
        $tokens->removeTrailingWhitespace($functionNameIndex + 6);
        // 6 = number of inserted tokens -1 for offset correction
    }
    /**
     * @param int $functionNameIndex
     */
    private function findSettypeNullCall(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $functionNameIndex, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $argumentToken)
    {
        $tokens->insertAt($functionNameIndex, [clone $argumentToken, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('='), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, 'null'])]);
        $tokens->removeTrailingWhitespace($functionNameIndex + 4);
        // 4 = number of inserted tokens -1 for offset correction
    }
}
