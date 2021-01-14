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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Phpdoc;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 */
final class PhpdocOrderByValueFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Order phpdoc tags by value.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @covers Foo
 * @covers Bar
 */
final class MyTest extends \\PHPUnit_Framework_TestCase
{}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @author Bob
 * @author Alice
 */
final class MyTest extends \\PHPUnit_Framework_TestCase
{}
', ['annotations' => ['author']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, PhpUnitFqcnAnnotationFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_DOC_COMMENT]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if ([] === $this->configuration['annotations']) {
            return;
        }
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            foreach ($this->configuration['annotations'] as $type) {
                $findPattern = \sprintf('/@%s\\s.+@%s\\s/s', $type, $type);
                if (!$tokens[$index]->isGivenKind(\T_DOC_COMMENT) || 0 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match($findPattern, $tokens[$index]->getContent())) {
                    continue;
                }
                $docBlock = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($tokens[$index]->getContent());
                $annotations = $docBlock->getAnnotationsOfType($type);
                $annotationMap = [];
                $replacePattern = \sprintf('/\\*\\s*@%s\\s+(.+)/', $type);
                foreach ($annotations as $annotation) {
                    $rawContent = $annotation->getContent();
                    $comparableContent = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace($replacePattern, '\\1', \strtolower(\trim($rawContent)));
                    $annotationMap[$comparableContent] = $rawContent;
                }
                $orderedAnnotationMap = $annotationMap;
                \ksort($orderedAnnotationMap, \SORT_STRING);
                if ($orderedAnnotationMap === $annotationMap) {
                    continue;
                }
                $lines = $docBlock->getLines();
                foreach (\array_reverse($annotations) as $annotation) {
                    \array_splice($lines, $annotation->getStart(), $annotation->getEnd() - $annotation->getStart() + 1, \array_pop($orderedAnnotationMap));
                }
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \implode('', $lines)]);
            }
        }
    }
    protected function createConfigurationDefinition()
    {
        $allowedValues = ['author', 'covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'internal', 'requires', 'throws', 'uses'];
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('annotations', 'List of annotations to order, e.g. `["covers"]`.'))->setAllowedTypes(['array'])->setAllowedValues([new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset($allowedValues)])->setDefault(['covers'])->getOption()]);
    }
}
