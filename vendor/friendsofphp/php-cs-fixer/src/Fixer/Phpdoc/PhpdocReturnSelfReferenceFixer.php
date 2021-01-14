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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options;
/**
 * @author SpacePossum
 */
final class PhpdocReturnSelfReferenceFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    private static $toTypes = ['$this', 'static', 'self'];
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('The type of `@return` annotations of methods returning a reference to itself must the configured one.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Sample
{
    /**
     * @return this
     */
    public function test1()
    {
        return $this;
    }

    /**
     * @return $self
     */
    public function test2()
    {
        return $this;
    }
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Sample
{
    /**
     * @return this
     */
    public function test1()
    {
        return $this;
    }

    /**
     * @return $self
     */
    public function test2()
    {
        return $this;
    }
}
', ['replacements' => ['this' => 'self']])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \count($tokens) > 10 && $tokens->isTokenKindFound(\T_DOC_COMMENT) && $tokens->isAnyTokenKindsFound([\T_CLASS, \T_INTERFACE]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer, PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return 10;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        foreach ($tokensAnalyzer->getClassyElements() as $index => $element) {
            if ('method' === $element['type']) {
                $this->fixMethod($tokens, $index);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $default = ['this' => '$this', '@this' => '$this', '$self' => 'self', '@self' => 'self', '$static' => 'static', '@static' => 'static'];
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless('replacements', [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('replacements', 'Mapping between replaced return types with new ones.'))->setAllowedTypes(['array'])->setNormalizer(static function (\_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options $options, $value) use($default) {
            $normalizedValue = [];
            foreach ($value as $from => $to) {
                if (\is_string($from)) {
                    $from = \strtolower($from);
                }
                if (!isset($default[$from])) {
                    throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Unknown key "%s", expected any of "%s".', \is_object($from) ? \get_class($from) : \gettype($from) . (\is_resource($from) ? '' : '#' . $from), \implode('", "', \array_keys($default))));
                }
                if (!\in_array($to, self::$toTypes, \true)) {
                    throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Unknown value "%s", expected any of "%s".', \is_object($to) ? \get_class($to) : \gettype($to) . (\is_resource($to) ? '' : '#' . $to), \implode('", "', self::$toTypes)));
                }
                $normalizedValue[$from] = $to;
            }
            return $normalizedValue;
        })->setDefault($default)->getOption()], $this->getName());
    }
    /**
     * @param int $index
     */
    private function fixMethod(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        static $methodModifiers = [\T_STATIC, \T_FINAL, \T_ABSTRACT, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC];
        // find PHPDoc of method (if any)
        do {
            $tokenIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$tokenIndex]->isGivenKind($methodModifiers)) {
                break;
            }
            $index = $tokenIndex;
        } while (\true);
        $docIndex = $tokens->getPrevNonWhitespace($index);
        if (!$tokens[$docIndex]->isGivenKind(\T_DOC_COMMENT)) {
            return;
        }
        // find @return
        $docBlock = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($tokens[$docIndex]->getContent());
        $returnsBlock = $docBlock->getAnnotationsOfType('return');
        if (!\count($returnsBlock)) {
            return;
            // no return annotation found
        }
        $returnsBlock = $returnsBlock[0];
        $types = $returnsBlock->getTypes();
        if (!\count($types)) {
            return;
            // no return type(s) found
        }
        $newTypes = [];
        foreach ($types as $type) {
            $lower = \strtolower($type);
            $newTypes[] = isset($this->configuration['replacements'][$lower]) ? $this->configuration['replacements'][$lower] : $type;
        }
        if ($types === $newTypes) {
            return;
        }
        $returnsBlock->setTypes($newTypes);
        $tokens[$docIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $docBlock->getContent()]);
    }
}
