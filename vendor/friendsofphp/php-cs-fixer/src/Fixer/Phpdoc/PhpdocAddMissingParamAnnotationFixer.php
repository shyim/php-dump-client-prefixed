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
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Line;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocAddMissingParamAnnotationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('PHPDoc should contain `@param` for all params.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param int $bar
 *
 * @return void
 */
function f9(string $foo, $bar, $baz) {}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param int $bar
 *
 * @return void
 */
function f9(string $foo, $bar, $baz) {}
', ['only_untyped' => \true]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param int $bar
 *
 * @return void
 */
function f9(string $foo, $bar, $baz) {}
', ['only_untyped' => \false])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAlignFixer, PhpdocAlignFixer, PhpdocOrderFixer.
     * Must run after CommentToPhpdocFixer, GeneralPhpdocTagRenameFixer, PhpdocIndentFixer, PhpdocNoAliasTagFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return 10;
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
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $argumentsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $tokenContent = $token->getContent();
            if (\false !== \stripos($tokenContent, 'inheritdoc')) {
                continue;
            }
            // ignore one-line phpdocs like `/** foo */`, as there is no place to put new annotations
            if (\false === \strpos($tokenContent, "\n")) {
                continue;
            }
            $mainIndex = $index;
            $index = $tokens->getNextMeaningfulToken($index);
            if (null === $index) {
                return;
            }
            while ($tokens[$index]->isGivenKind([\T_ABSTRACT, \T_FINAL, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \T_STATIC, \T_VAR])) {
                $index = $tokens->getNextMeaningfulToken($index);
            }
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $index = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            $arguments = [];
            foreach ($argumentsAnalyzer->getArguments($tokens, $openIndex, $index) as $start => $end) {
                $argumentInfo = $this->prepareArgumentInformation($tokens, $start, $end);
                if (!$this->configuration['only_untyped'] || '' === $argumentInfo['type']) {
                    $arguments[$argumentInfo['name']] = $argumentInfo;
                }
            }
            if (!\count($arguments)) {
                continue;
            }
            $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($tokenContent);
            $lastParamLine = null;
            foreach ($doc->getAnnotationsOfType('param') as $annotation) {
                $pregMatched = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/^[^$]+(\\$\\w+).*$/s', $annotation->getContent(), $matches);
                if (1 === $pregMatched) {
                    unset($arguments[$matches[1]]);
                }
                $lastParamLine = \max($lastParamLine, $annotation->getEnd());
            }
            if (!\count($arguments)) {
                continue;
            }
            $lines = $doc->getLines();
            $linesCount = \count($lines);
            \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/^(\\s*).*$/', $lines[$linesCount - 1]->getContent(), $matches);
            $indent = $matches[1];
            $newLines = [];
            foreach ($arguments as $argument) {
                $type = $argument['type'] ?: 'mixed';
                if ('?' !== $type[0] && 'null' === \strtolower($argument['default'])) {
                    $type = 'null|' . $type;
                }
                $newLines[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Line(\sprintf('%s* @param %s %s%s', $indent, $type, $argument['name'], $this->whitespacesConfig->getLineEnding()));
            }
            \array_splice($lines, $lastParamLine ? $lastParamLine + 1 : $linesCount - 1, 0, $newLines);
            $tokens[$mainIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \implode('', $lines)]);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('only_untyped', 'Whether to add missing `@param` annotations for untyped parameters only.'))->setDefault(\true)->setAllowedTypes(['bool'])->getOption()]);
    }
    /**
     * @param int $start
     * @param int $end
     *
     * @return array
     */
    private function prepareArgumentInformation(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $start, $end)
    {
        $info = ['default' => '', 'name' => '', 'type' => ''];
        $sawName = \false;
        for ($index = $start; $index <= $end; ++$index) {
            $token = $tokens[$index];
            if ($token->isComment() || $token->isWhitespace()) {
                continue;
            }
            if ($token->isGivenKind(\T_VARIABLE)) {
                $sawName = \true;
                $info['name'] = $token->getContent();
                continue;
            }
            if ($token->equals('=')) {
                continue;
            }
            if ($sawName) {
                $info['default'] .= $token->getContent();
            } elseif ('&' !== $token->getContent()) {
                if ($token->isGivenKind(\T_ELLIPSIS)) {
                    if ('' === $info['type']) {
                        $info['type'] = 'array';
                    } else {
                        $info['type'] .= '[]';
                    }
                } else {
                    $info['type'] .= $token->getContent();
                }
            }
        }
        return $info;
    }
}
