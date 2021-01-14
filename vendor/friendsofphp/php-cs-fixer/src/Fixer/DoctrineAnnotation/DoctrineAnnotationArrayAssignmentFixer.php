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
/**
 * Forces the configured operator for assignment in arrays in Doctrine Annotations.
 */
final class DoctrineAnnotationArrayAssignmentFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Doctrine annotations must use configured operator for assignment in arrays.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @Foo({bar : \"baz\"})\n */\nclass Bar {}\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @Foo({bar = \"baz\"})\n */\nclass Bar {}\n", ['operator' => ':'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before DoctrineAnnotationSpacesFixer.
     */
    public function getPriority()
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $options = parent::createConfigurationDefinition()->getOptions();
        $operator = new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('operator', 'The operator to use.');
        $options[] = $operator->setAllowedValues(['=', ':'])->setDefault('=')->getOption();
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver($options);
    }
    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $tokens)
    {
        $scopes = [];
        foreach ($tokens as $token) {
            if ($token->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                $scopes[] = 'annotation';
                continue;
            }
            if ($token->isType(\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_OPEN_CURLY_BRACES)) {
                $scopes[] = 'array';
                continue;
            }
            if ($token->isType([\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS, \_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_CLOSE_CURLY_BRACES])) {
                \array_pop($scopes);
                continue;
            }
            if ('array' === \end($scopes) && $token->isType([\_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_EQUALS, \_PhpScoper3fe455fa007d\Doctrine\Common\Annotations\DocLexer::T_COLON])) {
                $token->setContent($this->configuration['operator']);
            }
        }
    }
}
