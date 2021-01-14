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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Comment;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
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
 */
final class SingleLineCommentStyleFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @var bool
     */
    private $asteriskEnabled;
    /**
     * @var bool
     */
    private $hashEnabled;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->asteriskEnabled = \in_array('asterisk', $this->configuration['comment_types'], \true);
        $this->hashEnabled = \in_array('hash', $this->configuration['comment_types'], \true);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/* asterisk comment */
$a = 1;

# hash comment
$b = 2;

/*
 * multi-line
 * comment
 */
$c = 3;
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/* first comment */
$a = 1;

/*
 * second comment
 */
$b = 2;

/*
 * third
 * comment
 */
$c = 3;
', ['comment_types' => ['asterisk']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php # comment\n", ['comment_types' => ['hash']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after NoUselessReturnFixer.
     */
    public function getPriority()
    {
        return -19;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }
            $content = $token->getContent();
            $commentContent = \substr($content, 2, -2) ?: '';
            if ($this->hashEnabled && '#' === $content[0]) {
                if (isset($content[1]) && '[' === $content[1]) {
                    continue;
                    // This might be attribute on PHP8, do not change
                }
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), '//' . \substr($content, 1)]);
                continue;
            }
            if (!$this->asteriskEnabled || \false !== \strpos($commentContent, '?>') || '/*' !== \substr($content, 0, 2) || 1 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/[^\\s\\*].*\\R.*[^\\s\\*]/s', $commentContent)) {
                continue;
            }
            $nextTokenIndex = $index + 1;
            if (isset($tokens[$nextTokenIndex])) {
                $nextToken = $tokens[$nextTokenIndex];
                if (!$nextToken->isWhitespace() || 1 !== \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $nextToken->getContent())) {
                    continue;
                }
                $tokens[$nextTokenIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$nextToken->getId(), \ltrim($nextToken->getContent(), " \t")]);
            }
            $content = '//';
            if (1 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/[^\\s\\*]/', $commentContent)) {
                $content = '// ' . \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/[\\s\\*]*([^\\s\\*](?:.+[^\\s\\*])?)[\\s\\*]*/', '\\1', $commentContent);
            }
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), $content]);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('comment_types', 'List of comment types to fix'))->setAllowedTypes(['array'])->setAllowedValues([new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset(['asterisk', 'hash'])])->setDefault(['asterisk', 'hash'])->getOption()]);
    }
}
