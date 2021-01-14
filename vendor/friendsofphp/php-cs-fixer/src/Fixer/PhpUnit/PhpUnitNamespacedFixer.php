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

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitNamespacedFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private $originalClassRegEx;
    /**
     * Class Mappings.
     *
     *  * [original classname => new classname] Some classes which match the
     *    original class regular expression do not have a same-compound name-
     *    space class and need a dedicated translation table. This trans-
     *    lation table is defined in @see configure.
     *
     * @var array|string[] Class Mappings
     */
    private $classMap;
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $codeSample = '<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomething()
    {
        PHPUnit_Framework_Assert::assertTrue(true);
    }
}
';
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('PHPUnit classes MUST be used in namespaced version, e.g. `\\PHPUnit\\Framework\\TestCase` instead of `\\PHPUnit_Framework_TestCase`.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample($codeSample), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample($codeSample, ['target' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_4_8])], "PHPUnit v6 has finally fully switched to namespaces.\n" . "You could start preparing the upgrade by switching from non-namespaced TestCase to namespaced one.\n" . 'Forward compatibility layer (`\\PHPUnit\\Framework\\TestCase` class) was backported to PHPUnit v4.8.35 and PHPUnit v5.4.0.' . "\n" . 'Extended forward compatibility layer (`PHPUnit\\Framework\\Assert`, `PHPUnit\\Framework\\BaseTestListener`, `PHPUnit\\Framework\\TestListener` classes) was introduced in v5.7.0.' . "\n", 'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.');
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
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_6_0)) {
            $this->originalClassRegEx = '/^PHPUnit_\\w+$/i';
            // @noinspection ClassConstantCanBeUsedInspection
            $this->classMap = ['PHPUnit_Extensions_PhptTestCase' => '_PhpScoper3fe455fa007d\\PHPUnit\\Runner\\PhptTestCase', 'PHPUnit_Framework_Constraint' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\Constraint', 'PHPUnit_Framework_Constraint_StringMatches' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\StringMatchesFormatDescription', 'PHPUnit_Framework_Constraint_JsonMatches_ErrorMessageProvider' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\JsonMatchesErrorMessageProvider', 'PHPUnit_Framework_Constraint_PCREMatch' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\RegularExpression', 'PHPUnit_Framework_Constraint_ExceptionMessageRegExp' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\ExceptionMessageRegularExpression', 'PHPUnit_Framework_Constraint_And' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\LogicalAnd', 'PHPUnit_Framework_Constraint_Or' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\LogicalOr', 'PHPUnit_Framework_Constraint_Not' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\LogicalNot', 'PHPUnit_Framework_Constraint_Xor' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Constraint\\LogicalXor', 'PHPUnit_Framework_Error' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\Error\\Error', 'PHPUnit_Framework_TestSuite_DataProvider' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\DataProviderTestSuite', 'PHPUnit_Framework_MockObject_Invocation_Static' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\MockObject\\Invocation\\StaticInvocation', 'PHPUnit_Framework_MockObject_Invocation_Object' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\MockObject\\Invocation\\ObjectInvocation', 'PHPUnit_Framework_MockObject_Stub_Return' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\MockObject\\Stub\\ReturnStub', 'PHPUnit_Runner_Filter_Group_Exclude' => '_PhpScoper3fe455fa007d\\PHPUnit\\Runner\\Filter\\ExcludeGroupFilterIterator', 'PHPUnit_Runner_Filter_Group_Include' => '_PhpScoper3fe455fa007d\\PHPUnit\\Runner\\Filter\\IncludeGroupFilterIterator', 'PHPUnit_Runner_Filter_Test' => '_PhpScoper3fe455fa007d\\PHPUnit\\Runner\\Filter\\NameFilterIterator', 'PHPUnit_Util_PHP' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\PHP\\AbstractPhpProcess', 'PHPUnit_Util_PHP_Default' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\PHP\\DefaultPhpProcess', 'PHPUnit_Util_PHP_Windows' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\PHP\\WindowsPhpProcess', 'PHPUnit_Util_Regex' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\RegularExpression', 'PHPUnit_Util_TestDox_ResultPrinter_XML' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\TestDox\\XmlResultPrinter', 'PHPUnit_Util_TestDox_ResultPrinter_HTML' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\TestDox\\HtmlResultPrinter', 'PHPUnit_Util_TestDox_ResultPrinter_Text' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\TestDox\\TextResultPrinter', 'PHPUnit_Util_TestSuiteIterator' => '_PhpScoper3fe455fa007d\\PHPUnit\\Framework\\TestSuiteIterator', 'PHPUnit_Util_XML' => '_PhpScoper3fe455fa007d\\PHPUnit\\Util\\Xml'];
        } elseif (\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_7)) {
            $this->originalClassRegEx = '/^PHPUnit_Framework_TestCase|PHPUnit_Framework_Assert|PHPUnit_Framework_BaseTestListener|PHPUnit_Framework_TestListener$/i';
            $this->classMap = [];
        } else {
            $this->originalClassRegEx = '/^PHPUnit_Framework_TestCase$/i';
            $this->classMap = [];
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $importedOriginalClassesMap = [];
        $currIndex = 0;
        while (null !== $currIndex) {
            $currIndex = $tokens->getNextTokenOfKind($currIndex, [[\T_STRING]]);
            if (null === $currIndex) {
                break;
            }
            $originalClass = $tokens[$currIndex]->getContent();
            if (1 !== \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match($this->originalClassRegEx, $originalClass)) {
                ++$currIndex;
                continue;
            }
            $substituteTokens = $this->generateReplacement($originalClass);
            $tokens->clearAt($currIndex);
            $tokens->insertAt($currIndex, isset($importedOriginalClassesMap[$originalClass]) ? $substituteTokens[$substituteTokens->getSize() - 1] : $substituteTokens);
            $prevIndex = $tokens->getPrevMeaningfulToken($currIndex);
            if ($tokens[$prevIndex]->isGivenKind(\T_USE)) {
                $importedOriginalClassesMap[$originalClass] = \true;
            } elseif ($tokens[$prevIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
                if ($tokens[$prevIndex]->isGivenKind(\T_USE)) {
                    $importedOriginalClassesMap[$originalClass] = \true;
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('target', 'Target version of PHPUnit.'))->setAllowedTypes(['string'])->setAllowedValues([\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_4_8, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_5_7, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_6_0, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST])->setDefault(\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST)->getOption()]);
    }
    /**
     * @param string $originalClassName
     *
     * @return Tokens
     */
    private function generateReplacement($originalClassName)
    {
        $delimiter = '_';
        $string = $originalClassName;
        if (isset($this->classMap[$originalClassName])) {
            $delimiter = '\\';
            $string = $this->classMap[$originalClassName];
        }
        $parts = \explode($delimiter, $string);
        $tokensArray = [];
        while (!empty($parts)) {
            $tokensArray[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, \array_shift($parts)]);
            if (!empty($parts)) {
                $tokensArray[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']);
            }
        }
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromArray($tokensArray);
    }
}
