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
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation;
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Utils;
final class PhpdocTypesOrderFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Sorts PHPDoc types.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param string|null $bar
 */
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string $bar
 */
', ['null_adjustment' => 'always_last']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string|int|\\Foo $bar
 */
', ['sort_algorithm' => 'alpha']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string|int|\\Foo $bar
 */
', ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string|int|\\Foo $bar
 */
', ['sort_algorithm' => 'alpha', 'null_adjustment' => 'none'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, PhpdocAnnotationWithoutDotFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('sort_algorithm', 'The sorting algorithm to apply.'))->setAllowedValues(['alpha', 'none'])->setDefault('alpha')->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('null_adjustment', 'Forces the position of `null` (overrides `sort_algorithm`).'))->setAllowedValues(['always_first', 'always_last', 'none'])->setDefault('always_first')->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation::getTagsWithTypes());
            if (!\count($annotations)) {
                continue;
            }
            foreach ($annotations as $annotation) {
                $types = $annotation->getTypes();
                // fix main types
                $annotation->setTypes($this->sortTypes($types));
                // fix @method parameters types
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replaceCallback('/(@method\\s+.+?\\s+\\w+\\()(.*)\\)/', function (array $matches) {
                    $sorted = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replaceCallback('/([^\\s,]+)([\\s]+\\$[^\\s,]+)/', function (array $matches) {
                        return $this->sortJoinedTypes($matches[1]) . $matches[2];
                    }, $matches[2]);
                    return $matches[1] . $sorted . ')';
                }, $line->getContent()));
            }
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doc->getContent()]);
        }
    }
    /**
     * @param string[] $types
     *
     * @return string[]
     */
    private function sortTypes(array $types)
    {
        foreach ($types as $index => $type) {
            $types[$index] = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replaceCallback('/^([^<]+)<(?:([\\w\\|]+?)(,\\s*))?(.*)>$/', function (array $matches) {
                return $matches[1] . '<' . $this->sortJoinedTypes($matches[2]) . $matches[3] . $this->sortJoinedTypes($matches[4]) . '>';
            }, $type);
        }
        if ('alpha' === $this->configuration['sort_algorithm']) {
            $types = \_PhpScoper3fe455fa007d\PhpCsFixer\Utils::stableSort($types, static function ($type) {
                return $type;
            }, static function ($typeA, $typeB) {
                $regexp = '/^\\??\\\\?/';
                return \strcasecmp(\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace($regexp, '', $typeA), \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace($regexp, '', $typeB));
            });
        }
        if ('none' !== $this->configuration['null_adjustment']) {
            $nulls = [];
            foreach ($types as $index => $type) {
                if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/^\\\\?null$/i', $type)) {
                    $nulls[$index] = $type;
                    unset($types[$index]);
                }
            }
            if (\count($nulls)) {
                if ('always_last' === $this->configuration['null_adjustment']) {
                    \array_push($types, ...$nulls);
                } else {
                    \array_unshift($types, ...$nulls);
                }
            }
        }
        return $types;
    }
    /**
     * @param string $types
     *
     * @return string
     */
    private function sortJoinedTypes($types)
    {
        $types = \array_filter(\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::split('/([^|<]+(?:<.*>)?)/', $types, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY), static function ($value) {
            return '|' !== $value;
        });
        return \implode('|', $this->sortTypes($types));
    }
}
