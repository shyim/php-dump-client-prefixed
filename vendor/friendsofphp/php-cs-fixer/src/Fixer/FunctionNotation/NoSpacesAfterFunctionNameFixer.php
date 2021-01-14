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
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * Fixer for rules defined in PSR2 ¶4.6.
 *
 * @author Varga Bence <vbence@czentral.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoSpacesAfterFunctionNameFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nrequire ('sample.php');\necho (test (3));\nexit  (1);\n\$func ();\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before FunctionToConstantFixer.
     * Must run after PowToExponentiationFixer.
     */
    public function getPriority()
    {
        return 2;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(\array_merge($this->getFunctionyTokenKinds(), [\T_STRING]));
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $functionyTokens = $this->getFunctionyTokenKinds();
        $languageConstructionTokens = $this->getLanguageConstructionTokenKinds();
        $braceTypes = $this->getBraceAfterVariableKinds();
        foreach ($tokens as $index => $token) {
            // looking for start brace
            if (!$token->equals('(')) {
                continue;
            }
            // last non-whitespace token, can never be `null` always at least PHP open tag before it
            $lastTokenIndex = $tokens->getPrevNonWhitespace($index);
            // check for ternary operator
            $endParenthesisIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
            $nextNonWhiteSpace = $tokens->getNextMeaningfulToken($endParenthesisIndex);
            if (null !== $nextNonWhiteSpace && $tokens[$nextNonWhiteSpace]->equals('?') && $tokens[$lastTokenIndex]->isGivenKind($languageConstructionTokens)) {
                continue;
            }
            // check if it is a function call
            if ($tokens[$lastTokenIndex]->isGivenKind($functionyTokens)) {
                $this->fixFunctionCall($tokens, $index);
            } elseif ($tokens[$lastTokenIndex]->isGivenKind(\T_STRING)) {
                // for real function calls or definitions
                $possibleDefinitionIndex = $tokens->getPrevMeaningfulToken($lastTokenIndex);
                if (!$tokens[$possibleDefinitionIndex]->isGivenKind(\T_FUNCTION)) {
                    $this->fixFunctionCall($tokens, $index);
                }
            } elseif ($tokens[$lastTokenIndex]->equalsAny($braceTypes)) {
                $block = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::detectBlockType($tokens[$lastTokenIndex]);
                if (\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE === $block['type'] || \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE === $block['type'] || \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE === $block['type'] || \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE === $block['type']) {
                    $this->fixFunctionCall($tokens, $index);
                }
            }
        }
    }
    /**
     * Fixes whitespaces around braces of a function(y) call.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     */
    private function fixFunctionCall(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        // remove space before opening brace
        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens->clearAt($index - 1);
        }
    }
    /**
     * @return array<array|string>
     */
    private function getBraceAfterVariableKinds()
    {
        static $tokens = [')', ']', [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_CLOSE], [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE]];
        return $tokens;
    }
    /**
     * Gets the token kinds which can work as function calls.
     *
     * @return int[] Token names
     */
    private function getFunctionyTokenKinds()
    {
        static $tokens = [\T_ARRAY, \T_ECHO, \T_EMPTY, \T_EVAL, \T_EXIT, \T_INCLUDE, \T_INCLUDE_ONCE, \T_ISSET, \T_LIST, \T_PRINT, \T_REQUIRE, \T_REQUIRE_ONCE, \T_UNSET, \T_VARIABLE];
        return $tokens;
    }
    /**
     * Gets the token kinds of actually language construction.
     *
     * @return int[]
     */
    private function getLanguageConstructionTokenKinds()
    {
        static $languageConstructionTokens = [\T_ECHO, \T_PRINT, \T_INCLUDE, \T_INCLUDE_ONCE, \T_REQUIRE, \T_REQUIRE_ONCE];
        return $languageConstructionTokens;
    }
}
