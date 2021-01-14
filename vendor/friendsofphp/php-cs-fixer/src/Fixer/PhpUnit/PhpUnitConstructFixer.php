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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    private static $assertionFixers = ['assertSame' => 'fixAssertPositive', 'assertEquals' => 'fixAssertPositive', 'assertNotEquals' => 'fixAssertNegative', 'assertNotSame' => 'fixAssertNegative'];
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
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('PHPUnit assertion method calls like `->assertSame(true, $foo)` should be written with dedicated method like `->assertTrue($foo)`.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class FooTest extends \\PHPUnit_Framework_TestCase {
    public function testSomething() {
        $this->assertEquals(false, $b);
        $this->assertSame(true, $a);
        $this->assertNotEquals(null, $c);
        $this->assertNotSame(null, $d);
    }
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class FooTest extends \\PHPUnit_Framework_TestCase {
    public function testSomething() {
        $this->assertEquals(false, $b);
        $this->assertSame(true, $a);
        $this->assertNotEquals(null, $c);
        $this->assertNotSame(null, $d);
    }
}
', ['assertions' => ['assertSame', 'assertNotSame']])], null, 'Fixer could be risky if one is overriding PHPUnit\'s native methods.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpUnitDedicateAssertFixer.
     */
    public function getPriority()
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        // no assertions to be fixed - fast return
        if (empty($this->configuration['assertions'])) {
            return;
        }
        foreach ($this->configuration['assertions'] as $assertionMethod) {
            $assertionFixer = self::$assertionFixers[$assertionMethod];
            for ($index = $startIndex; $index < $endIndex; ++$index) {
                $index = $this->{$assertionFixer}($tokens, $index, $assertionMethod);
                if (null === $index) {
                    break;
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless('assertions', [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('assertions', 'List of assertion methods to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset(\array_keys(self::$assertionFixers))])->setDefault(['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame'])->getOption()], $this->getName());
    }
    /**
     * @param int    $index
     * @param string $method
     *
     * @return null|int
     */
    private function fixAssertNegative(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $method)
    {
        static $map = ['false' => 'assertNotFalse', 'null' => 'assertNotNull', 'true' => 'assertNotTrue'];
        return $this->fixAssert($map, $tokens, $index, $method);
    }
    /**
     * @param int    $index
     * @param string $method
     *
     * @return null|int
     */
    private function fixAssertPositive(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $method)
    {
        static $map = ['false' => 'assertFalse', 'null' => 'assertNull', 'true' => 'assertTrue'];
        return $this->fixAssert($map, $tokens, $index, $method);
    }
    /**
     * @param array<string, string> $map
     * @param int                   $index
     * @param string                $method
     *
     * @return null|int
     */
    private function fixAssert(array $map, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $method)
    {
        $functionsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        $sequence = $tokens->findSequence([[\T_STRING, $method], '('], $index);
        if (null === $sequence) {
            return null;
        }
        $sequenceIndexes = \array_keys($sequence);
        if (!$functionsAnalyzer->isTheSameClassCall($tokens, $sequenceIndexes[0])) {
            return null;
        }
        $sequenceIndexes[2] = $tokens->getNextMeaningfulToken($sequenceIndexes[1]);
        $firstParameterToken = $tokens[$sequenceIndexes[2]];
        if (!$firstParameterToken->isNativeConstant()) {
            return $sequenceIndexes[2];
        }
        $sequenceIndexes[3] = $tokens->getNextMeaningfulToken($sequenceIndexes[2]);
        // return if first method argument is an expression, not value
        if (!$tokens[$sequenceIndexes[3]]->equals(',')) {
            return $sequenceIndexes[3];
        }
        $tokens[$sequenceIndexes[0]] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, $map[\strtolower($firstParameterToken->getContent())]]);
        $tokens->clearRange($sequenceIndexes[2], $tokens->getNextNonWhitespace($sequenceIndexes[3]) - 1);
        return $sequenceIndexes[3];
    }
}
