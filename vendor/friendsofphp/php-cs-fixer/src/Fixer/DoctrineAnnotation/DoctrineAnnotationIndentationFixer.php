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
use _PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
final class DoctrineAnnotationIndentationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Doctrine annotations must be indented with four spaces.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n *  @Foo(\n *   foo=\"foo\"\n *  )\n */\nclass Bar {}\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n *  @Foo({@Bar,\n *   @Baz})\n */\nclass Bar {}\n", ['indent_mixed_lines' => \true])]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver(\array_merge(parent::createConfigurationDefinition()->getOptions(), [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('indent_mixed_lines', 'Whether to indent lines that have content before closing parenthesis.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption()]));
    }
    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens)
    {
        $annotationPositions = [];
        for ($index = 0, $max = \count($tokens); $index < $max; ++$index) {
            if (!$tokens[$index]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_AT)) {
                continue;
            }
            $annotationEndIndex = $tokens->getAnnotationEnd($index);
            if (null === $annotationEndIndex) {
                return;
            }
            $annotationPositions[] = [$index, $annotationEndIndex];
            $index = $annotationEndIndex;
        }
        $indentLevel = 0;
        foreach ($tokens as $index => $token) {
            if (!$token->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_NONE) || \false === \strpos($token->getContent(), "\n")) {
                continue;
            }
            if (!$this->indentationCanBeFixed($tokens, $index, $annotationPositions)) {
                continue;
            }
            $braces = $this->getLineBracesCount($tokens, $index);
            $delta = $braces[0] - $braces[1];
            $mixedBraces = 0 === $delta && $braces[0] > 0;
            $extraIndentLevel = 0;
            if ($indentLevel > 0 && ($delta < 0 || $mixedBraces)) {
                --$indentLevel;
                if ($this->configuration['indent_mixed_lines'] && $this->isClosingLineWithMeaningfulContent($tokens, $index)) {
                    $extraIndentLevel = 1;
                }
            }
            $token->setContent(\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(\\n( +\\*)?) *$/', '$1' . \str_repeat(' ', 4 * ($indentLevel + $extraIndentLevel) + 1), $token->getContent()));
            if ($delta > 0 || $mixedBraces) {
                ++$indentLevel;
            }
        }
    }
    /**
     * @param int $index
     *
     * @return int[]
     */
    private function getLineBracesCount(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens, $index)
    {
        $opening = 0;
        $closing = 0;
        while (isset($tokens[++$index])) {
            $token = $tokens[$index];
            if ($token->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_NONE) && \false !== \strpos($token->getContent(), "\n")) {
                break;
            }
            if ($token->isType([\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS, \_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_CURLY_BRACES])) {
                ++$opening;
                continue;
            }
            if (!$token->isType([\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS, \_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_CURLY_BRACES])) {
                continue;
            }
            if ($opening > 0) {
                --$opening;
            } else {
                ++$closing;
            }
        }
        return [$opening, $closing];
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isClosingLineWithMeaningfulContent(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens, $index)
    {
        while (isset($tokens[++$index])) {
            $token = $tokens[$index];
            if ($token->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_NONE)) {
                if (\false !== \strpos($token->getContent(), "\n")) {
                    return \false;
                }
                continue;
            }
            return !$token->isType([\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS, \_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_CURLY_BRACES]);
        }
        return \false;
    }
    /**
     * @param int               $newLineTokenIndex
     * @param array<array<int>> $annotationPositions Pairs of begin and end indexes of main annotations
     *
     * @return bool
     */
    private function indentationCanBeFixed(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens, $newLineTokenIndex, array $annotationPositions)
    {
        foreach ($annotationPositions as $position) {
            if ($newLineTokenIndex >= $position[0] && $newLineTokenIndex <= $position[1]) {
                return \true;
            }
        }
        for ($index = $newLineTokenIndex + 1, $max = \count($tokens); $index < $max; ++$index) {
            $token = $tokens[$index];
            if (\false !== \strpos($token->getContent(), "\n")) {
                return \false;
            }
            return $tokens[$index]->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_AT);
        }
        return \false;
    }
}
