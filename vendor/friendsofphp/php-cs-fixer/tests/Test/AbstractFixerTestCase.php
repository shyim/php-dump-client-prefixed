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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Tests\Test;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSampleInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Linter\CachingLinter;
use _PhpScoper3fe455fa007d\PhpCsFixer\Linter\Linter;
use _PhpScoper3fe455fa007d\PhpCsFixer\Linter\LinterInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Linter\ProcessLinter;
use _PhpScoper3fe455fa007d\PhpCsFixer\StdinFileInfo;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tests\TestCase;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\Prophecy\Argument;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractFixerTestCase extends \_PhpScoper3fe455fa007d\PhpCsFixer\Tests\TestCase
{
    use AssertTokensTrait;
    use IsIdenticalConstraint;
    /**
     * @var null|LinterInterface
     */
    protected $linter;
    /**
     * @var null|AbstractFixer
     */
    protected $fixer;
    // do not modify this structure without prior discussion
    private $allowedRequiredOptions = ['header_comment' => ['header' => \true]];
    // do not modify this structure without prior discussion
    private $allowedFixersWithoutDefaultCodeSample = ['general_phpdoc_annotation_remove' => \true, 'general_phpdoc_tag_rename' => \true];
    protected function doSetUp()
    {
        parent::doSetUp();
        $this->linter = $this->getLinter();
        $this->fixer = $this->createFixer();
        // @todo remove at 3.0 together with env var itself
        if (\getenv('PHP_CS_FIXER_TEST_USE_LEGACY_TOKENIZER')) {
            \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::setLegacyMode(\true);
        }
    }
    protected function doTearDown()
    {
        parent::doTearDown();
        $this->linter = null;
        $this->fixer = null;
        // @todo remove at 3.0
        \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::setLegacyMode(\false);
    }
    public final function testIsRisky()
    {
        static::assertIsBool($this->fixer->isRisky(), \sprintf('Return type for ::isRisky of "%s" is invalid.', $this->fixer->getName()));
        if ($this->fixer->isRisky()) {
            self::assertValidDescription($this->fixer->getName(), 'risky description', $this->fixer->getDefinition()->getRiskyDescription());
        } else {
            static::assertNull($this->fixer->getDefinition()->getRiskyDescription(), \sprintf('[%s] Fixer is not risky so no description of it expected.', $this->fixer->getName()));
        }
        if ($this->fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer) {
            return;
        }
        $reflection = new \ReflectionMethod($this->fixer, 'isRisky');
        // If fixer is not risky then the method `isRisky` from `AbstractFixer` must be used
        static::assertSame(!$this->fixer->isRisky(), \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer::class === $reflection->getDeclaringClass()->getName());
    }
    public final function testFixerDefinitions()
    {
        static::assertInstanceOf(\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface::class, $this->fixer);
        $fixerName = $this->fixer->getName();
        $definition = $this->fixer->getDefinition();
        $fixerIsConfigurable = $this->fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
        self::assertValidDescription($fixerName, 'summary', $definition->getSummary());
        $samples = $definition->getCodeSamples();
        static::assertNotEmpty($samples, \sprintf('[%s] Code samples are required.', $fixerName));
        $configSamplesProvided = [];
        $dummyFileInfo = new \_PhpScoper3fe455fa007d\PhpCsFixer\StdinFileInfo();
        foreach ($samples as $sampleCounter => $sample) {
            static::assertInstanceOf(\_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSampleInterface::class, $sample, \sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));
            static::assertIsInt($sampleCounter);
            $code = $sample->getCode();
            static::assertIsString($code, \sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));
            static::assertNotEmpty($code, \sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));
            if (!$this->fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer) {
                static::assertSame("\n", \substr($code, -1), \sprintf('[%s] Sample #%d must end with linebreak', $fixerName, $sampleCounter));
            }
            $config = $sample->getConfiguration();
            if (null !== $config) {
                static::assertTrue($fixerIsConfigurable, \sprintf('[%s] Sample #%d has configuration, but the fixer is not configurable.', $fixerName, $sampleCounter));
                static::assertIsArray($config, \sprintf('[%s] Sample #%d configuration must be an array or null.', $fixerName, $sampleCounter));
                $configSamplesProvided[$sampleCounter] = $config;
            } elseif ($fixerIsConfigurable) {
                if (!$sample instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface) {
                    static::assertArrayNotHasKey('default', $configSamplesProvided, \sprintf('[%s] Multiple non-versioned samples with default configuration.', $fixerName));
                }
                $configSamplesProvided['default'] = \true;
            }
            if ($sample instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(\PHP_VERSION_ID)) {
                continue;
            }
            if ($fixerIsConfigurable) {
                // always re-configure as the fixer might have been configured with diff. configuration form previous sample
                $this->fixer->configure(null === $config ? [] : $config);
            }
            \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::clearCache();
            $tokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($code);
            $this->fixer->fix($sample instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : $dummyFileInfo, $tokens);
            static::assertTrue($tokens->isChanged(), \sprintf('[%s] Sample #%d is not changed during fixing.', $fixerName, $sampleCounter));
            $duplicatedCodeSample = \array_search($sample, \array_slice($samples, 0, $sampleCounter), \false);
            static::assertFalse($duplicatedCodeSample, \sprintf('[%s] Sample #%d duplicates #%d.', $fixerName, $sampleCounter, $duplicatedCodeSample));
        }
        if ($fixerIsConfigurable) {
            if (isset($configSamplesProvided['default'])) {
                \reset($configSamplesProvided);
                static::assertSame('default', \key($configSamplesProvided), \sprintf('[%s] First sample must be for the default configuration.', $fixerName));
            } elseif (!isset($this->allowedFixersWithoutDefaultCodeSample[$fixerName])) {
                static::assertArrayHasKey($fixerName, $this->allowedRequiredOptions, \sprintf('[%s] Has no sample for default configuration.', $fixerName));
            }
            // It may only shrink, never add anything to it.
            $fixerNamesWithKnownMissingSamplesWithConfig = [
                // @TODO 3.0 - remove this
                'is_null',
            ];
            if (\count($configSamplesProvided) < 2) {
                if (\in_array($fixerName, $fixerNamesWithKnownMissingSamplesWithConfig, \true)) {
                    static::markTestIncomplete(\sprintf('[%s] Configurable fixer only provides a default configuration sample and none for its configuration options, please help and add it.', $fixerName));
                }
                static::fail(\sprintf('[%s] Configurable fixer only provides a default configuration sample and none for its configuration options.', $fixerName));
            } elseif (\in_array($fixerName, $fixerNamesWithKnownMissingSamplesWithConfig, \true)) {
                static::fail(\sprintf('[%s] Invalid listed as missing code samples, please update the list.', $fixerName));
            }
            $options = $this->fixer->getConfigurationDefinition()->getOptions();
            foreach ($options as $option) {
                static::assertMatchesRegularExpression('/^[a-z_]+[a-z]$/', $option->getName(), \sprintf('[%s] Option %s is not snake_case.', $fixerName, $option->getName()));
            }
        }
    }
    /**
     * @group legacy
     * @expectedDeprecation PhpCsFixer\FixerDefinition\FixerDefinition::getConfigurationDescription is deprecated and will be removed in 3.0.
     * @expectedDeprecation PhpCsFixer\FixerDefinition\FixerDefinition::getDefaultConfiguration is deprecated and will be removed in 3.0.
     */
    public final function testLegacyFixerDefinitions()
    {
        $definition = $this->fixer->getDefinition();
        static::assertNull($definition->getConfigurationDescription(), \sprintf('[%s] No configuration description expected.', $this->fixer->getName()));
        static::assertNull($definition->getDefaultConfiguration(), \sprintf('[%s] No default configuration expected.', $this->fixer->getName()));
    }
    public final function testFixersAreFinal()
    {
        $reflection = new \ReflectionClass($this->fixer);
        static::assertTrue($reflection->isFinal(), \sprintf('Fixer "%s" must be declared "final".', $this->fixer->getName()));
    }
    public final function testFixersAreDefined()
    {
        static::assertInstanceOf(\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface::class, $this->fixer);
    }
    public final function testDeprecatedFixersHaveCorrectSummary()
    {
        $reflection = new \ReflectionClass($this->fixer);
        $comment = $reflection->getDocComment();
        static::assertStringNotContainsString('DEPRECATED', $this->fixer->getDefinition()->getSummary(), 'Fixer cannot contain word "DEPRECATED" in summary');
        if ($this->fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface) {
            static::assertStringContainsString('@deprecated', $comment);
        } elseif (\is_string($comment)) {
            static::assertStringNotContainsString('@deprecated', $comment);
        }
    }
    public final function testFixerConfigurationDefinitions()
    {
        if (!$this->fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface) {
            $this->addToAssertionCount(1);
            // not applied to the fixer without configuration
            return;
        }
        $configurationDefinition = $this->fixer->getConfigurationDefinition();
        static::assertInstanceOf(\_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface::class, $configurationDefinition);
        foreach ($configurationDefinition->getOptions() as $option) {
            static::assertInstanceOf(\_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionInterface::class, $option);
            static::assertNotEmpty($option->getDescription());
            static::assertSame(!isset($this->allowedRequiredOptions[$this->fixer->getName()][$option->getName()]), $option->hasDefault(), \sprintf($option->hasDefault() ? 'Option `%s` of fixer `%s` is wrongly listed in `$allowedRequiredOptions` structure, as it is not required. If you just changed that option to not be required anymore, please adjust mentioned structure.' : 'Option `%s` of fixer `%s` shall not be required. If you want to introduce new required option please adjust `$allowedRequiredOptions` structure.', $option->getName(), $this->fixer->getName()));
            static::assertStringNotContainsString('DEPRECATED', $option->getDescription(), 'Option description cannot contain word "DEPRECATED"');
        }
    }
    public final function testFixersReturnTypes()
    {
        $tokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode('<?php ');
        $emptyTokens = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens();
        static::assertIsInt($this->fixer->getPriority(), \sprintf('Return type for ::getPriority of "%s" is invalid.', $this->fixer->getName()));
        static::assertIsBool($this->fixer->supports(new \SplFileInfo(__FILE__)), \sprintf('Return type for ::supports of "%s" is invalid.', $this->fixer->getName()));
        static::assertIsBool($this->fixer->isCandidate($emptyTokens), \sprintf('Return type for ::isCandidate with empty tokens of "%s" is invalid.', $this->fixer->getName()));
        static::assertFalse($emptyTokens->isChanged());
        static::assertIsBool($this->fixer->isCandidate($tokens), \sprintf('Return type for ::isCandidate of "%s" is invalid.', $this->fixer->getName()));
        static::assertFalse($tokens->isChanged());
        if ($this->fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Comment\HeaderCommentFixer) {
            $this->fixer->configure(['header' => 'a']);
        }
        static::assertNull($this->fixer->fix(new \SplFileInfo(__FILE__), $emptyTokens), \sprintf('Return type for ::fix with empty tokens of "%s" is invalid.', $this->fixer->getName()));
        static::assertFalse($emptyTokens->isChanged());
        static::assertNull($this->fixer->fix(new \SplFileInfo(__FILE__), $tokens), \sprintf('Return type for ::fix of "%s" is invalid.', $this->fixer->getName()));
    }
    /**
     * @return AbstractFixer
     */
    protected function createFixer()
    {
        $fixerClassName = \preg_replace('/^(PhpCsFixer)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);
        return new $fixerClassName();
    }
    /**
     * @param string $filename
     *
     * @return \SplFileInfo
     */
    protected function getTestFile($filename = __FILE__)
    {
        static $files = [];
        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }
        return $files[$filename];
    }
    /**
     * Tests if a fixer fixes a given string to match the expected result.
     *
     * It is used both if you want to test if something is fixed or if it is not touched by the fixer.
     * It also makes sure that the expected output does not change when run through the fixer. That means that you
     * do not need two test cases like [$expected] and [$expected, $input] (where $expected is the same in both cases)
     * as the latter covers both of them.
     * This method throws an exception if $expected and $input are equal to prevent test cases that accidentally do
     * not test anything.
     *
     * @param string            $expected The expected fixer output
     * @param null|string       $input    The fixer input, or null if it should intentionally be equal to the output
     * @param null|\SplFileInfo $file     The file to fix, or null if unneeded
     */
    protected function doTest($expected, $input = null, \SplFileInfo $file = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }
        $file = $file ?: $this->getTestFile();
        $fileIsSupported = $this->fixer->supports($file);
        if (null !== $input) {
            static::assertNull($this->lintSource($input));
            \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::clearCache();
            $tokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($input);
            if ($fileIsSupported) {
                static::assertTrue($this->fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                static::assertFalse($tokens->isChanged(), 'Fixer must not touch Tokens on candidate check.');
                $fixResult = $this->fixer->fix($file, $tokens);
                static::assertNull($fixResult, '->fix method must return null.');
            }
            static::assertThat($tokens->generateCode(), self::createIsIdenticalStringConstraint($expected), 'Code build on input code must match expected code.');
            static::assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');
            $tokens->clearEmptyTokens();
            static::assertSame(\count($tokens), \count(\array_unique(\array_map(static function (\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token) {
                return \spl_object_hash($token);
            }, $tokens->toArray()))), 'Token items inside Tokens collection must be unique.');
            \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::clearCache();
            $expectedTokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($expected);
            static::assertTokens($expectedTokens, $tokens);
        }
        static::assertNull($this->lintSource($expected));
        \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::clearCache();
        $tokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($expected);
        if ($fileIsSupported) {
            $fixResult = $this->fixer->fix($file, $tokens);
            static::assertNull($fixResult, '->fix method must return null.');
        }
        static::assertThat($tokens->generateCode(), self::createIsIdenticalStringConstraint($expected), 'Code build on expected code must not change.');
        static::assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
    }
    /**
     * @param string $source
     *
     * @return null|string
     */
    protected function lintSource($source)
    {
        try {
            $this->linter->lintSource($source)->check();
        } catch (\Exception $e) {
            return $e->getMessage() . "\n\nSource:\n{$source}";
        }
        return null;
    }
    /**
     * @return LinterInterface
     */
    private function getLinter()
    {
        static $linter = null;
        if (null === $linter) {
            if (\getenv('SKIP_LINT_TEST_CASES')) {
                $linterProphecy = $this->prophesize(\_PhpScoper3fe455fa007d\PhpCsFixer\Linter\LinterInterface::class);
                $linterProphecy->lintSource(\_PhpScoper3fe455fa007d\Prophecy\Argument::type('string'))->willReturn($this->prophesize(\_PhpScoper3fe455fa007d\PhpCsFixer\Linter\LintingResultInterface::class)->reveal());
                $linter = $linterProphecy->reveal();
            } else {
                $linter = new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\CachingLinter(\getenv('FAST_LINT_TEST_CASES') ? new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\Linter() : new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\ProcessLinter());
            }
        }
        return $linter;
    }
    /**
     * @param string $fixerName
     * @param string $descriptionType
     * @param mixed  $description
     */
    private static function assertValidDescription($fixerName, $descriptionType, $description)
    {
        static::assertIsString($description);
        static::assertMatchesRegularExpression('/^[A-Z`][^"]+\\.$/', $description, \sprintf('[%s] The %s must start with capital letter or a ` and end with dot.', $fixerName, $descriptionType));
        static::assertStringNotContainsString('phpdocs', $description, \sprintf('[%s] `PHPDoc` must not be in the plural in %s.', $fixerName, $descriptionType));
        static::assertCorrectCasing($description, 'PHPDoc', \sprintf('[%s] `PHPDoc` must be in correct casing in %s.', $fixerName, $descriptionType));
        static::assertCorrectCasing($description, 'PHPUnit', \sprintf('[%s] `PHPUnit` must be in correct casing in %s.', $fixerName, $descriptionType));
        static::assertFalse(\strpos($descriptionType, '``'), \sprintf('[%s] The %s must no contain sequential backticks.', $fixerName, $descriptionType));
    }
    /**
     * @param string $needle
     * @param string $haystack
     * @param string $message
     */
    private static function assertCorrectCasing($needle, $haystack, $message)
    {
        static::assertSame(\substr_count(\strtolower($haystack), \strtolower($needle)), \substr_count($haystack, $needle), $message);
    }
}
