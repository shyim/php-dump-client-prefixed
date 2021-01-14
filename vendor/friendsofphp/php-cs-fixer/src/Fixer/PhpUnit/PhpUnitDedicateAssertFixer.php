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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitDedicateAssertFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    private static $fixMap = ['array_key_exists' => ['assertArrayNotHasKey', 'assertArrayHasKey'], 'empty' => ['assertNotEmpty', 'assertEmpty'], 'file_exists' => ['assertFileNotExists', 'assertFileExists'], 'is_array' => \true, 'is_bool' => \true, 'is_callable' => \true, 'is_dir' => ['assertDirectoryNotExists', 'assertDirectoryExists'], 'is_double' => \true, 'is_float' => \true, 'is_infinite' => ['assertFinite', 'assertInfinite'], 'is_int' => \true, 'is_integer' => \true, 'is_long' => \true, 'is_nan' => [\false, 'assertNan'], 'is_null' => ['assertNotNull', 'assertNull'], 'is_numeric' => \true, 'is_object' => \true, 'is_readable' => ['assertNotIsReadable', 'assertIsReadable'], 'is_real' => \true, 'is_resource' => \true, 'is_scalar' => \true, 'is_string' => \true, 'is_writable' => ['assertNotIsWritable', 'assertIsWritable']];
    /**
     * @var string[]
     */
    private $functions = [];
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if (isset($this->configuration['functions'])) {
            $this->functions = $this->configuration['functions'];
            return;
        }
        // assertions added in 3.0: assertArrayNotHasKey assertArrayHasKey assertFileNotExists assertFileExists assertNotNull, assertNull
        $this->functions = ['array_key_exists', 'file_exists', 'is_null'];
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_3_5)) {
            // assertions added in 3.5: assertInternalType assertNotEmpty assertEmpty
            $this->functions = \array_merge($this->functions, ['empty', 'is_array', 'is_bool', 'is_boolean', 'is_callable', 'is_double', 'is_float', 'is_int', 'is_integer', 'is_long', 'is_numeric', 'is_object', 'is_real', 'is_resource', 'is_scalar', 'is_string']);
        }
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_0)) {
            // assertions added in 5.0: assertFinite assertInfinite assertNan
            $this->functions = \array_merge($this->functions, ['is_infinite', 'is_nan']);
        }
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_6)) {
            // assertions added in 5.6: assertDirectoryExists assertDirectoryNotExists assertIsReadable assertNotIsReadable assertIsWritable assertNotIsWritable
            $this->functions = \array_merge($this->functions, ['is_dir', 'is_readable', 'is_writable']);
        }
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
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('PHPUnit assertions like `assertInternalType`, `assertFileExists`, should be used over `assertTrue`.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertTrue(is_float( $a), "my message");
        $this->assertTrue(is_nan($a));
    }
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertTrue(is_dir($a));
        $this->assertTrue(is_writable($a));
        $this->assertTrue(is_readable($a));
    }
}
', ['target' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_6])], null, 'Fixer could be risky if one is overriding PHPUnit\'s native methods.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpUnitDedicateAssertInternalTypeFixer.
     * Must run after NoAliasFunctionsFixer, PhpUnitConstructFixer.
     */
    public function getPriority()
    {
        return -15;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        foreach ($this->getPreviousAssertCall($tokens, $startIndex, $endIndex) as $assertCall) {
            // test and fix for assertTrue/False to dedicated asserts
            if ('asserttrue' === $assertCall['loweredName'] || 'assertfalse' === $assertCall['loweredName']) {
                $this->fixAssertTrueFalse($tokens, $assertCall);
                continue;
            }
            if ('assertsame' === $assertCall['loweredName'] || 'assertnotsame' === $assertCall['loweredName'] || 'assertequals' === $assertCall['loweredName'] || 'assertnotequals' === $assertCall['loweredName']) {
                $this->fixAssertSameEquals($tokens, $assertCall);
                continue;
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $values = ['array_key_exists', 'empty', 'file_exists', 'is_array', 'is_bool', 'is_callable', 'is_double', 'is_float', 'is_infinite', 'is_int', 'is_integer', 'is_long', 'is_nan', 'is_null', 'is_numeric', 'is_object', 'is_real', 'is_resource', 'is_scalar', 'is_string'];
        \sort($values);
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless('functions', [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('functions', 'List of assertions to fix (overrides `target`).'))->setAllowedTypes(['null', 'array'])->setAllowedValues([null, new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset($values)])->setDefault(null)->setDeprecationMessage('Use option `target` instead.')->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('target', 'Target version of PHPUnit.'))->setAllowedTypes(['string'])->setAllowedValues([\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_3_0, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_3_5, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_0, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_6, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST])->setDefault(\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_0)->getOption()], $this->getName());
    }
    private function fixAssertTrueFalse(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $assertCall)
    {
        $testDefaultNamespaceTokenIndex = \false;
        $testIndex = $tokens->getNextMeaningfulToken($assertCall['openBraceIndex']);
        if (!$tokens[$testIndex]->isGivenKind([\T_EMPTY, \T_STRING])) {
            if (!$tokens[$testIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                return;
            }
            $testDefaultNamespaceTokenIndex = $testIndex;
            $testIndex = $tokens->getNextMeaningfulToken($testIndex);
        }
        $testOpenIndex = $tokens->getNextMeaningfulToken($testIndex);
        if (!$tokens[$testOpenIndex]->equals('(')) {
            return;
        }
        $testCloseIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $testOpenIndex);
        $assertCallCloseIndex = $tokens->getNextMeaningfulToken($testCloseIndex);
        if (!$tokens[$assertCallCloseIndex]->equalsAny([')', ','])) {
            return;
        }
        $isPositive = 'asserttrue' === $assertCall['loweredName'];
        $content = \strtolower($tokens[$testIndex]->getContent());
        if (!\in_array($content, $this->functions, \true)) {
            return;
        }
        if (\is_array(self::$fixMap[$content])) {
            if (\false !== self::$fixMap[$content][$isPositive]) {
                $tokens[$assertCall['index']] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, self::$fixMap[$content][$isPositive]]);
                $this->removeFunctionCall($tokens, $testDefaultNamespaceTokenIndex, $testIndex, $testOpenIndex, $testCloseIndex);
            }
            return;
        }
        $type = \substr($content, 3);
        $tokens[$assertCall['index']] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, $isPositive ? 'assertInternalType' : 'assertNotInternalType']);
        $tokens[$testIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_CONSTANT_ENCAPSED_STRING, "'" . $type . "'"]);
        $tokens[$testOpenIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(',');
        $tokens->clearTokenAndMergeSurroundingWhitespace($testCloseIndex);
        $commaIndex = $tokens->getPrevMeaningfulToken($testCloseIndex);
        if ($tokens[$commaIndex]->equals(',')) {
            $tokens->removeTrailingWhitespace($commaIndex);
            $tokens->clearAt($commaIndex);
        }
        if (!$tokens[$testOpenIndex + 1]->isWhitespace()) {
            $tokens->insertAt($testOpenIndex + 1, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
        }
        if (\false !== $testDefaultNamespaceTokenIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($testDefaultNamespaceTokenIndex);
        }
    }
    private function fixAssertSameEquals(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $assertCall)
    {
        // @ $this->/self::assertEquals/Same([$nextIndex])
        $expectedIndex = $tokens->getNextMeaningfulToken($assertCall['openBraceIndex']);
        // do not fix
        // let $a = [1,2]; $b = "2";
        // "$this->assertEquals("2", count($a)); $this->assertEquals($b, count($a)); $this->assertEquals(2.1, count($a));"
        if (!$tokens[$expectedIndex]->isGivenKind(\T_LNUMBER)) {
            return;
        }
        // @ $this->/self::assertEquals/Same([$nextIndex,$commaIndex])
        $commaIndex = $tokens->getNextMeaningfulToken($expectedIndex);
        if (!$tokens[$commaIndex]->equals(',')) {
            return;
        }
        // @ $this->/self::assertEquals/Same([$nextIndex,$commaIndex,$countCallIndex])
        $countCallIndex = $tokens->getNextMeaningfulToken($commaIndex);
        if ($tokens[$countCallIndex]->isGivenKind(\T_NS_SEPARATOR)) {
            $defaultNamespaceTokenIndex = $countCallIndex;
            $countCallIndex = $tokens->getNextMeaningfulToken($countCallIndex);
        } else {
            $defaultNamespaceTokenIndex = \false;
        }
        if (!$tokens[$countCallIndex]->isGivenKind(\T_STRING)) {
            return;
        }
        $lowerContent = \strtolower($tokens[$countCallIndex]->getContent());
        if ('count' !== $lowerContent && 'sizeof' !== $lowerContent) {
            return;
            // not a call to "count" or "sizeOf"
        }
        // @ $this->/self::assertEquals/Same([$nextIndex,$commaIndex,[$defaultNamespaceTokenIndex,]$countCallIndex,$countCallOpenBraceIndex])
        $countCallOpenBraceIndex = $tokens->getNextMeaningfulToken($countCallIndex);
        if (!$tokens[$countCallOpenBraceIndex]->equals('(')) {
            return;
        }
        $countCallCloseBraceIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $countCallOpenBraceIndex);
        $afterCountCallCloseBraceIndex = $tokens->getNextMeaningfulToken($countCallCloseBraceIndex);
        if (!$tokens[$afterCountCallCloseBraceIndex]->equalsAny([')', ','])) {
            return;
        }
        $this->removeFunctionCall($tokens, $defaultNamespaceTokenIndex, $countCallIndex, $countCallOpenBraceIndex, $countCallCloseBraceIndex);
        $tokens[$assertCall['index']] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, \false === \strpos($assertCall['loweredName'], 'not', 6) ? 'assertCount' : 'assertNotCount']);
    }
    /**
     * @param int $startIndex
     * @param int $endIndex
     */
    private function getPreviousAssertCall(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $functionsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        for ($index = $endIndex; $index > $startIndex; --$index) {
            $index = $tokens->getPrevTokenOfKind($index, [[\T_STRING]]);
            if (null === $index) {
                return;
            }
            // test if "assert" something call
            $loweredContent = \strtolower($tokens[$index]->getContent());
            if ('assert' !== \substr($loweredContent, 0, 6)) {
                continue;
            }
            // test candidate for simple calls like: ([\]+'some fixable call'(...))
            $openBraceIndex = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$openBraceIndex]->equals('(')) {
                continue;
            }
            if (!$functionsAnalyzer->isTheSameClassCall($tokens, $index)) {
                continue;
            }
            (yield ['index' => $index, 'loweredName' => $loweredContent, 'openBraceIndex' => $openBraceIndex, 'closeBraceIndex' => $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex)]);
        }
    }
    /**
     * @param false|int $callNSIndex
     * @param int       $callIndex
     * @param int       $openIndex
     * @param int       $closeIndex
     */
    private function removeFunctionCall(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $callNSIndex, $callIndex, $openIndex, $closeIndex)
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($callIndex);
        if (\false !== $callNSIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($callNSIndex);
        }
        $tokens->clearTokenAndMergeSurroundingWhitespace($openIndex);
        $commaIndex = $tokens->getPrevMeaningfulToken($closeIndex);
        if ($tokens[$commaIndex]->equals(',')) {
            $tokens->removeTrailingWhitespace($commaIndex);
            $tokens->clearAt($commaIndex);
        }
        $tokens->clearTokenAndMergeSurroundingWhitespace($closeIndex);
    }
}
