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
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitExpectationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var array<string, string>
     */
    private $methodMap = [];
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->methodMap = ['setExpectedException' => 'expectExceptionMessage'];
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_6)) {
            $this->methodMap['setExpectedExceptionRegExp'] = 'expectExceptionMessageRegExp';
        }
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_8_4)) {
            $this->methodMap['setExpectedExceptionRegExp'] = 'expectExceptionMessageMatches';
            $this->methodMap['expectExceptionMessageRegExp'] = 'expectExceptionMessageMatches';
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Usages of `->setExpectedException*` methods MUST be replaced by `->expectException*` methods.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", null, 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
', ['target' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_8_4]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", null, 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
', ['target' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_6]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
', ['target' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_2])], null, 'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run after PhpUnitNoExpectationAnnotationFixer.
     */
    public function getPriority()
    {
        return 0;
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
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('target', 'Target version of PHPUnit.'))->setAllowedTypes(['string'])->setAllowedValues([\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_2, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_6, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_8_4, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST])->setDefault(\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST)->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $argumentsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        $oldMethodSequence = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_VARIABLE, '$this']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_OBJECT_OPERATOR, '->']), [\T_STRING]];
        for ($index = $startIndex; $startIndex < $endIndex; ++$index) {
            $match = $tokens->findSequence($oldMethodSequence, $index);
            if (null === $match) {
                return;
            }
            list($thisIndex, , $index) = \array_keys($match);
            if (!isset($this->methodMap[$tokens[$index]->getContent()])) {
                continue;
            }
            $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $closeIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            $commaIndex = $tokens->getPrevMeaningfulToken($closeIndex);
            if ($tokens[$commaIndex]->equals(',')) {
                $tokens->removeTrailingWhitespace($commaIndex);
                $tokens->clearAt($commaIndex);
            }
            $arguments = $argumentsAnalyzer->getArguments($tokens, $openIndex, $closeIndex);
            $argumentsCnt = \count($arguments);
            $argumentsReplacements = ['expectException', $this->methodMap[$tokens[$index]->getContent()], 'expectExceptionCode'];
            $indent = $this->whitespacesConfig->getLineEnding() . \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $thisIndex);
            $isMultilineWhitespace = \false;
            for ($cnt = $argumentsCnt - 1; $cnt >= 1; --$cnt) {
                $argStart = \array_keys($arguments)[$cnt];
                $argBefore = $tokens->getPrevMeaningfulToken($argStart);
                if ('expectExceptionMessage' === $argumentsReplacements[$cnt]) {
                    $paramIndicatorIndex = $tokens->getNextMeaningfulToken($argBefore);
                    $afterParamIndicatorIndex = $tokens->getNextMeaningfulToken($paramIndicatorIndex);
                    if ($tokens[$paramIndicatorIndex]->equals([\T_STRING, 'null'], \false) && $tokens[$afterParamIndicatorIndex]->equals(')')) {
                        if ($tokens[$argBefore + 1]->isWhitespace()) {
                            $tokens->clearTokenAndMergeSurroundingWhitespace($argBefore + 1);
                        }
                        $tokens->clearTokenAndMergeSurroundingWhitespace($argBefore);
                        $tokens->clearTokenAndMergeSurroundingWhitespace($paramIndicatorIndex);
                        continue;
                    }
                }
                $isMultilineWhitespace = $isMultilineWhitespace || $tokens[$argStart]->isWhitespace() && !$tokens[$argStart]->isWhitespace(" \t");
                $tokensOverrideArgStart = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $indent]), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_VARIABLE, '$this']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_OBJECT_OPERATOR, '->']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, $argumentsReplacements[$cnt]]), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('(')];
                $tokensOverrideArgBefore = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(')'), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(';')];
                if ($isMultilineWhitespace) {
                    $tokensOverrideArgStart[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $indent . $this->whitespacesConfig->getIndent()]);
                    \array_unshift($tokensOverrideArgBefore, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $indent]));
                }
                if ($tokens[$argStart]->isWhitespace()) {
                    $tokens->overrideRange($argStart, $argStart, $tokensOverrideArgStart);
                } else {
                    $tokens->insertAt($argStart, $tokensOverrideArgStart);
                }
                $tokens->overrideRange($argBefore, $argBefore, $tokensOverrideArgBefore);
            }
            $methodName = 'expectException';
            if ('expectExceptionMessageRegExp' === $tokens[$index]->getContent()) {
                $methodName = $this->methodMap[$tokens[$index]->getContent()];
            }
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, $methodName]);
        }
    }
}
