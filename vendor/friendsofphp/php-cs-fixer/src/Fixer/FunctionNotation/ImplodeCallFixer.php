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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\FunctionNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class ImplodeCallFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Function `implode` must be called with 2 arguments in the documented order.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nimplode(\$pieces, '');\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nimplode(\$pieces);\n")], null, 'Risky when the function `implode` is overridden.');
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MethodArgumentSpaceFixer.
     * Must run after NoAliasFunctionsFixer.
     */
    public function getPriority()
    {
        return -1;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $functionsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->equals([\T_STRING, 'implode'], \false)) {
                continue;
            }
            if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $argumentsIndices = $this->getArgumentIndices($tokens, $index);
            if (1 === \count($argumentsIndices)) {
                $firstArgumentIndex = \key($argumentsIndices);
                $tokens->insertAt($firstArgumentIndex, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_CONSTANT_ENCAPSED_STRING, "''"]), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(','), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
                continue;
            }
            if (2 === \count($argumentsIndices)) {
                list($firstArgumentIndex, $secondArgumentIndex) = \array_keys($argumentsIndices);
                // If the first argument is string we have nothing to do
                if ($tokens[$firstArgumentIndex]->isGivenKind(\T_CONSTANT_ENCAPSED_STRING)) {
                    continue;
                }
                // If the second argument is not string we cannot make a swap
                if (!$tokens[$secondArgumentIndex]->isGivenKind(\T_CONSTANT_ENCAPSED_STRING)) {
                    continue;
                }
                // collect tokens from first argument
                $firstArgumentEndIndex = $argumentsIndices[\key($argumentsIndices)];
                $newSecondArgumentTokens = [];
                for ($i = \key($argumentsIndices); $i <= $firstArgumentEndIndex; ++$i) {
                    $newSecondArgumentTokens[] = clone $tokens[$i];
                    $tokens->clearAt($i);
                }
                $tokens->insertAt($firstArgumentIndex, clone $tokens[$secondArgumentIndex]);
                // insert above increased the second argument index
                ++$secondArgumentIndex;
                $tokens->clearAt($secondArgumentIndex);
                $tokens->insertAt($secondArgumentIndex, $newSecondArgumentTokens);
            }
        }
    }
    /**
     * @param int $functionNameIndex
     *
     * @return array<int, int> In the format: startIndex => endIndex
     */
    private function getArgumentIndices(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $functionNameIndex)
    {
        $argumentsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        $openParenthesis = $tokens->getNextTokenOfKind($functionNameIndex, ['(']);
        $closeParenthesis = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis);
        $indices = [];
        foreach ($argumentsAnalyzer->getArguments($tokens, $openParenthesis, $closeParenthesis) as $startIndexCandidate => $endIndex) {
            $indices[$tokens->getNextMeaningfulToken($startIndexCandidate - 1)] = $tokens->getPrevMeaningfulToken($endIndex + 1);
        }
        return $indices;
    }
}
