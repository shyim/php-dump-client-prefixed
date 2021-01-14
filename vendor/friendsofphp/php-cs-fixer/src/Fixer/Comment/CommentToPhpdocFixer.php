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
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Utils;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class CommentToPhpdocFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var string[]
     */
    private $ignoredTags = [];
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
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * Must run before GeneralPhpdocAnnotationRemoveFixer, GeneralPhpdocTagRenameFixer, NoBlankLinesAfterPhpdocFixer, NoEmptyPhpdocFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocAlignFixer, PhpdocAnnotationWithoutDotFixer, PhpdocInlineTagFixer, PhpdocInlineTagNormalizerFixer, PhpdocLineSpanFixer, PhpdocNoAccessFixer, PhpdocNoAliasTagFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocNoUselessInheritdocFixer, PhpdocOrderByValueFixer, PhpdocOrderFixer, PhpdocReturnSelfReferenceFixer, PhpdocSeparationFixer, PhpdocSingleLineVarSpacingFixer, PhpdocSummaryFixer, PhpdocTagCasingFixer, PhpdocTagTypeFixer, PhpdocToCommentFixer, PhpdocToParamTypeFixer, PhpdocToReturnTypeFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer, PhpdocTrimFixer, PhpdocTypesOrderFixer, PhpdocVarAnnotationCorrectOrderFixer, PhpdocVarWithoutNameFixer.
     */
    public function getPriority()
    {
        // Should be run before all other PHPDoc fixers
        return 26;
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Comments with annotation should be docblock when used on structural elements.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php /* header */ \$x = true; /* @var bool \$isFoo */ \$isFoo = true;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n// @todo do something later\n\$foo = 1;\n\n// @var int \$a\n\$a = foo();\n", ['ignored_tags' => ['todo']])], null, 'Risky as new docblocks might mean more, e.g. a Doctrine entity might have a new column in database.');
    }
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->ignoredTags = \array_map(static function ($tag) {
            return \strtolower($tag);
        }, $this->configuration['ignored_tags']);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('ignored_tags', 'List of ignored tags'))->setAllowedTypes(['array'])->setDefault([])->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $commentsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer();
        for ($index = 0, $limit = \count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }
            if ($commentsAnalyzer->isHeaderComment($tokens, $index)) {
                continue;
            }
            if (!$commentsAnalyzer->isBeforeStructuralElement($tokens, $index)) {
                continue;
            }
            $commentIndices = $commentsAnalyzer->getCommentBlockIndices($tokens, $index);
            if ($this->isCommentCandidate($tokens, $commentIndices)) {
                $this->fixComment($tokens, $commentIndices);
            }
            $index = \max($commentIndices);
        }
    }
    /**
     * @param int[] $indices
     *
     * @return bool
     */
    private function isCommentCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $indices)
    {
        return \array_reduce($indices, function ($carry, $index) use($tokens) {
            if ($carry) {
                return \true;
            }
            if (1 !== \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('~(?:#|//|/\\*+|\\R(?:\\s*\\*)?)\\s*\\@([a-zA-Z0-9_\\\\-]+)(?=\\s|\\(|$)~', $tokens[$index]->getContent(), $matches)) {
                return \false;
            }
            return !\in_array(\strtolower($matches[1]), $this->ignoredTags, \true);
        }, \false);
    }
    /**
     * @param int[] $indices
     */
    private function fixComment(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $indices)
    {
        if (1 === \count($indices)) {
            $this->fixCommentSingleLine($tokens, \reset($indices));
        } else {
            $this->fixCommentMultiLine($tokens, $indices);
        }
    }
    /**
     * @param int $index
     */
    private function fixCommentSingleLine(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $message = $this->getMessage($tokens[$index]->getContent());
        if ('' !== \trim(\substr($message, 0, 1))) {
            $message = ' ' . $message;
        }
        if ('' !== \trim(\substr($message, -1))) {
            $message .= ' ';
        }
        $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, '/**' . $message . '*/']);
    }
    /**
     * @param int[] $indices
     */
    private function fixCommentMultiLine(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $indices)
    {
        $startIndex = \reset($indices);
        $indent = \_PhpScoper3fe455fa007d\PhpCsFixer\Utils::calculateTrailingWhitespaceIndent($tokens[$startIndex - 1]);
        $newContent = '/**' . $this->whitespacesConfig->getLineEnding();
        $count = \max($indices);
        for ($index = $startIndex; $index <= $count; ++$index) {
            if (!$tokens[$index]->isComment()) {
                continue;
            }
            if (\false !== \strpos($tokens[$index]->getContent(), '*/')) {
                return;
            }
            $newContent .= $indent . ' *' . $this->getMessage($tokens[$index]->getContent()) . $this->whitespacesConfig->getLineEnding();
        }
        for ($index = $startIndex; $index <= $count; ++$index) {
            $tokens->clearAt($index);
        }
        $newContent .= $indent . ' */';
        $tokens->insertAt($startIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $newContent]));
    }
    private function getMessage($content)
    {
        if (0 === \strpos($content, '#')) {
            return \substr($content, 1);
        }
        if (0 === \strpos($content, '//')) {
            return \substr($content, 2);
        }
        return \rtrim(\ltrim($content, '/*'), '*/');
    }
}
