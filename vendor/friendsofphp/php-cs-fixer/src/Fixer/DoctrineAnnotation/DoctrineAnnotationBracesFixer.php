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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DoctrineAnnotation;

use _PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer;
use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractDoctrineAnnotationFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * Adds braces to Doctrine annotations when missing.
 */
final class DoctrineAnnotationBracesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Doctrine annotations without arguments must use the configured syntax.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @Foo()\n */\nclass Bar {}\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @Foo\n */\nclass Bar {}\n", ['syntax' => 'with_braces'])]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver(\array_merge(parent::createConfigurationDefinition()->getOptions(), [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('syntax', 'Whether to add or remove braces.'))->setAllowedValues(['with_braces', 'without_braces'])->setDefault('without_braces')->getOption()]));
    }
    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens)
    {
        if ('without_braces' === $this->configuration['syntax']) {
            $this->removesBracesFromAnnotations($tokens);
        } else {
            $this->addBracesToAnnotations($tokens);
        }
    }
    private function addBracesToAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$tokens[$index]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_AT)) {
                continue;
            }
            $braceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if (null !== $braceIndex && $tokens[$braceIndex]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                continue;
            }
            $tokens->insertAt($index + 2, new \_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Token(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS, '('));
            $tokens->insertAt($index + 3, new \_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Token(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS, ')'));
        }
    }
    private function removesBracesFromAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens)
    {
        for ($index = 0, $max = \count($tokens); $index < $max; ++$index) {
            if (!$tokens[$index]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_AT)) {
                continue;
            }
            $openBraceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if (null === $openBraceIndex) {
                continue;
            }
            if (!$tokens[$openBraceIndex]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                continue;
            }
            $closeBraceIndex = $tokens->getNextMeaningfulToken($openBraceIndex);
            if (null === $closeBraceIndex) {
                continue;
            }
            if (!$tokens[$closeBraceIndex]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS)) {
                continue;
            }
            for ($currentIndex = $index + 2; $currentIndex <= $closeBraceIndex; ++$currentIndex) {
                $tokens[$currentIndex]->clear();
            }
        }
    }
}
