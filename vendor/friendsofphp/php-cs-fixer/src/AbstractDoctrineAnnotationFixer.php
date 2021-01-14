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
namespace _PhpScoper3fe455fa007d\PhpCsFixer;

use _PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token as PhpToken;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens as PhpTokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @internal
 */
abstract class AbstractDoctrineAnnotationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @var array
     */
    private $classyElements;
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
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $phpTokens)
    {
        // fetch indexes one time, this is safe as we never add or remove a token during fixing
        $analyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($phpTokens);
        $this->classyElements = $analyzer->getClassyElements();
        /** @var PhpToken $docCommentToken */
        foreach ($phpTokens->findGivenKind(\T_DOC_COMMENT) as $index => $docCommentToken) {
            if (!$this->nextElementAcceptsDoctrineAnnotations($phpTokens, $index)) {
                continue;
            }
            $tokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens::createFromDocComment($docCommentToken, $this->configuration['ignored_tags']);
            $this->fixAnnotations($tokens);
            $phpTokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $tokens->getCode()]);
        }
    }
    /**
     * Fixes Doctrine annotations from the given PHPDoc style comment.
     */
    protected abstract function fixAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Doctrine\Annotation\Tokens $doctrineAnnotationTokens);
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('ignored_tags', 'List of tags that must not be treated as Doctrine Annotations.'))->setAllowedTypes(['array'])->setAllowedValues([static function ($values) {
            foreach ($values as $value) {
                if (!\is_string($value)) {
                    return \false;
                }
            }
            return \true;
        }])->setDefault([
            // PHPDocumentor 1
            'abstract',
            'access',
            'code',
            'deprec',
            'encode',
            'exception',
            'final',
            'ingroup',
            'inheritdoc',
            'inheritDoc',
            'magic',
            'name',
            'toc',
            'tutorial',
            'private',
            'static',
            'staticvar',
            'staticVar',
            'throw',
            // PHPDocumentor 2
            'api',
            'author',
            'category',
            'copyright',
            'deprecated',
            'example',
            'filesource',
            'global',
            'ignore',
            'internal',
            'license',
            'link',
            'method',
            'package',
            'param',
            'property',
            'property-read',
            'property-write',
            'return',
            'see',
            'since',
            'source',
            'subpackage',
            'throws',
            'todo',
            'TODO',
            'usedBy',
            'uses',
            'var',
            'version',
            // PHPUnit
            'after',
            'afterClass',
            'backupGlobals',
            'backupStaticAttributes',
            'before',
            'beforeClass',
            'codeCoverageIgnore',
            'codeCoverageIgnoreStart',
            'codeCoverageIgnoreEnd',
            'covers',
            'coversDefaultClass',
            'coversNothing',
            'dataProvider',
            'depends',
            'expectedException',
            'expectedExceptionCode',
            'expectedExceptionMessage',
            'expectedExceptionMessageRegExp',
            'group',
            'large',
            'medium',
            'preserveGlobalState',
            'requires',
            'runTestsInSeparateProcesses',
            'runInSeparateProcess',
            'small',
            'test',
            'testdox',
            'ticket',
            'uses',
            // PHPCheckStyle
            'SuppressWarnings',
            // PHPStorm
            'noinspection',
            // PEAR
            'package_version',
            // PlantUML
            'enduml',
            'startuml',
            // other
            'fix',
            'FIXME',
            'fixme',
            'override',
        ])->getOption()]);
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function nextElementAcceptsDoctrineAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getNextMeaningfulToken($index);
            if (null === $index) {
                return \false;
            }
        } while ($tokens[$index]->isGivenKind([\T_ABSTRACT, \T_FINAL]));
        if ($tokens[$index]->isClassy()) {
            return \true;
        }
        while ($tokens[$index]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_NS_SEPARATOR, \T_STRING, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE])) {
            $index = $tokens->getNextMeaningfulToken($index);
        }
        return isset($this->classyElements[$index]);
    }
}
