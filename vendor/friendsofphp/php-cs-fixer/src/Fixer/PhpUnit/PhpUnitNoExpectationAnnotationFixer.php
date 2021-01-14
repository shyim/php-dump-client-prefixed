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

use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation;
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitNoExpectationAnnotationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var bool
     */
    private $fixMessageRegExp;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->fixMessageRegExp = \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::fulfills($this->configuration['target'], \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_4_3);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Usages of `@expectedException*` annotations MUST be replaced by `->setExpectedException*` methods.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    /**
     * @expectedException FooException
     * @expectedExceptionMessageRegExp /foo.*$/
     * @expectedExceptionCode 123
     */
    function testAaa()
    {
        aaa();
    }
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    /**
     * @expectedException FooException
     * @expectedExceptionCode 123
     */
    function testBbb()
    {
        bbb();
    }

    /**
     * @expectedException FooException
     * @expectedExceptionMessageRegExp /foo.*$/
     */
    function testCcc()
    {
        ccc();
    }
}
', ['target' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_3_2])], null, 'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpUnitExpectationFixer.
     */
    public function getPriority()
    {
        return 10;
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
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('target', 'Target version of PHPUnit.'))->setAllowedTypes(['string'])->setAllowedValues([\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_3_2, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_4_3, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST])->setDefault(\_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion::VERSION_NEWEST)->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('use_class_const', 'Use ::class notation.'))->setAllowedTypes(['bool'])->setDefault(\true)->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (!$tokens[$i]->isGivenKind(\T_FUNCTION) || $tokensAnalyzer->isLambda($i)) {
                continue;
            }
            $functionIndex = $i;
            $docBlockIndex = $i;
            // ignore abstract functions
            $braceIndex = $tokens->getNextTokenOfKind($functionIndex, [';', '{']);
            if (!$tokens[$braceIndex]->equals('{')) {
                continue;
            }
            do {
                $docBlockIndex = $tokens->getPrevNonWhitespace($docBlockIndex);
            } while ($tokens[$docBlockIndex]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT]));
            if (!$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($tokens[$docBlockIndex]->getContent());
            $annotations = [];
            foreach ($doc->getAnnotationsOfType(['expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp']) as $annotation) {
                $tag = $annotation->getTag()->getName();
                $content = $this->extractContentFromAnnotation($annotation);
                $annotations[$tag] = $content;
                $annotation->remove();
            }
            if (!isset($annotations['expectedException'])) {
                continue;
            }
            if (!$this->fixMessageRegExp && isset($annotations['expectedExceptionMessageRegExp'])) {
                continue;
            }
            $originalIndent = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);
            $paramList = $this->annotationsToParamList($annotations);
            $newMethodsCode = '<?php $this->' . (isset($annotations['expectedExceptionMessageRegExp']) ? 'setExpectedExceptionRegExp' : 'setExpectedException') . '(' . \implode(', ', $paramList) . ');';
            $newMethods = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($newMethodsCode);
            $newMethods[0] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $this->whitespacesConfig->getLineEnding() . $originalIndent . $this->whitespacesConfig->getIndent()]);
            // apply changes
            $docContent = $doc->getContent();
            if ('' === $docContent) {
                $docContent = '/** */';
            }
            $tokens[$docBlockIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $docContent]);
            $tokens->insertAt($braceIndex + 1, $newMethods);
            $whitespaceIndex = $braceIndex + $newMethods->getSize() + 1;
            $tokens[$whitespaceIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $this->whitespacesConfig->getLineEnding() . $tokens[$whitespaceIndex]->getContent()]);
            $i = $docBlockIndex;
        }
    }
    /**
     * @return string
     */
    private function extractContentFromAnnotation(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation $annotation)
    {
        $tag = $annotation->getTag()->getName();
        if (1 !== \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/@' . $tag . '\\s+(.+)$/s', $annotation->getContent(), $matches)) {
            return '';
        }
        $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\*+\\/$/', '', $matches[1]);
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/u', $content)) {
            $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\s*\\R+\\s*\\*\\s*/u', ' ', $content);
        }
        return \rtrim($content);
    }
    private function annotationsToParamList(array $annotations)
    {
        $params = [];
        $exceptionClass = \ltrim($annotations['expectedException'], '\\');
        if ($this->configuration['use_class_const']) {
            $params[] = "\\{$exceptionClass}::class";
        } else {
            $params[] = "'{$exceptionClass}'";
        }
        if (isset($annotations['expectedExceptionMessage'])) {
            $params[] = \var_export($annotations['expectedExceptionMessage'], \true);
        } elseif (isset($annotations['expectedExceptionMessageRegExp'])) {
            $params[] = \var_export($annotations['expectedExceptionMessageRegExp'], \true);
        } elseif (isset($annotations['expectedExceptionCode'])) {
            $params[] = 'null';
        }
        if (isset($annotations['expectedExceptionCode'])) {
            $params[] = $annotations['expectedExceptionCode'];
        }
        return $params;
    }
}
