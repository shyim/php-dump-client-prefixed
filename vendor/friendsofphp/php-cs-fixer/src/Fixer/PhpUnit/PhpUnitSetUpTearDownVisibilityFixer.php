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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit;

use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Gert de Pagter
 */
final class PhpUnitSetUpTearDownVisibilityFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Changes the visibility of the `setUp()` and `tearDown()` functions of PHPUnit to `protected`, to match the PHPUnit TestCase.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    private $hello;
    public function setUp()
    {
        $this->hello = "hello";
    }

    public function tearDown()
    {
        $this->hello = null;
    }
}
')], null, 'This fixer may change functions named `setUp()` or `tearDown()` outside of PHPUnit tests, ' . 'when a class is wrongly seen as a PHPUnit test.');
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
    protected function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $counter = 0;
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (2 === $counter) {
                break;
                // we've seen both method we are interested in, so stop analyzing this class
            }
            if (!$this->isSetupOrTearDownMethod($tokens, $i)) {
                continue;
            }
            ++$counter;
            $visibility = $tokensAnalyzer->getMethodAttributes($i)['visibility'];
            if (\T_PUBLIC === $visibility) {
                $index = $tokens->getPrevTokenOfKind($i, [[\T_PUBLIC]]);
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_PROTECTED, 'protected']);
                continue;
            }
            if (null === $visibility) {
                $tokens->insertAt($i, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_PROTECTED, 'protected']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
            }
        }
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isSetupOrTearDownMethod(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $isMethod = $tokens[$index]->isGivenKind(\T_FUNCTION) && !$tokensAnalyzer->isLambda($index);
        if (!$isMethod) {
            return \false;
        }
        $functionNameIndex = $tokens->getNextMeaningfulToken($index);
        $functionName = \strtolower($tokens[$functionNameIndex]->getContent());
        return 'setup' === $functionName || 'teardown' === $functionName;
    }
}
