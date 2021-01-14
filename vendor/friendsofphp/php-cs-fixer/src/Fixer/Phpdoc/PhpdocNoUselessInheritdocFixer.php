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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * Remove inheritdoc tags from classy that does not inherit.
 *
 * @author SpacePossum
 */
final class PhpdocNoUselessInheritdocFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Classy that does not inherit must not have `@inheritdoc` tags.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n/** {@inheritdoc} */\nclass Sample\n{\n}\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nclass Sample\n{\n    /**\n     * @inheritdoc\n     */\n    public function Test()\n    {\n    }\n}\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, NoTrailingWhitespaceInCommentFixer, PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return 6;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT) && $tokens->isAnyTokenKindsFound([\T_CLASS, \T_INTERFACE]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        // min. offset 4 as minimal candidate is @: <?php\n/** @inheritdoc */class min{}
        for ($index = 1, $count = \count($tokens) - 4; $index < $count; ++$index) {
            if ($tokens[$index]->isGivenKind([\T_CLASS, \T_INTERFACE])) {
                $index = $this->fixClassy($tokens, $index);
            }
        }
    }
    /**
     * @param int $index
     *
     * @return int
     */
    private function fixClassy(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        // figure out where the classy starts
        $classOpenIndex = $tokens->getNextTokenOfKind($index, ['{']);
        // figure out where the classy ends
        $classEndIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpenIndex);
        // is classy extending or implementing some interface
        $extendingOrImplementing = $this->isExtendingOrImplementing($tokens, $index, $classOpenIndex);
        if (!$extendingOrImplementing) {
            // PHPDoc of classy should not have inherit tag even when using traits as Traits cannot provide this information
            $this->fixClassyOutside($tokens, $index);
        }
        // figure out if the classy uses a trait
        if (!$extendingOrImplementing && $this->isUsingTrait($tokens, $index, $classOpenIndex, $classEndIndex)) {
            $extendingOrImplementing = \true;
        }
        $this->fixClassyInside($tokens, $classOpenIndex, $classEndIndex, !$extendingOrImplementing);
        return $classEndIndex;
    }
    /**
     * @param int  $classOpenIndex
     * @param int  $classEndIndex
     * @param bool $fixThisLevel
     */
    private function fixClassyInside(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classOpenIndex, $classEndIndex, $fixThisLevel)
    {
        for ($i = $classOpenIndex; $i < $classEndIndex; ++$i) {
            if ($tokens[$i]->isGivenKind(\T_CLASS)) {
                $i = $this->fixClassy($tokens, $i);
            } elseif ($fixThisLevel && $tokens[$i]->isGivenKind(\T_DOC_COMMENT)) {
                $this->fixToken($tokens, $i);
            }
        }
    }
    /**
     * @param int $classIndex
     */
    private function fixClassyOutside(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classIndex)
    {
        $previousIndex = $tokens->getPrevNonWhitespace($classIndex);
        if ($tokens[$previousIndex]->isGivenKind(\T_DOC_COMMENT)) {
            $this->fixToken($tokens, $previousIndex);
        }
    }
    /**
     * @param int $tokenIndex
     */
    private function fixToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $tokenIndex)
    {
        $count = 0;
        $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replaceCallback('#(\\h*(?:@{*|{*\\h*@)\\h*inheritdoc\\h*)([^}]*)((?:}*)\\h*)#i', static function ($matches) {
            return ' ' . $matches[2];
        }, $tokens[$tokenIndex]->getContent(), -1, $count);
        if ($count) {
            $tokens[$tokenIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $content]);
        }
    }
    /**
     * @param int $classIndex
     * @param int $classOpenIndex
     *
     * @return bool
     */
    private function isExtendingOrImplementing(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classIndex, $classOpenIndex)
    {
        for ($index = $classIndex; $index < $classOpenIndex; ++$index) {
            if ($tokens[$index]->isGivenKind([\T_EXTENDS, \T_IMPLEMENTS])) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @param int $classIndex
     * @param int $classOpenIndex
     * @param int $classCloseIndex
     *
     * @return bool
     */
    private function isUsingTrait(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classIndex, $classOpenIndex, $classCloseIndex)
    {
        if ($tokens[$classIndex]->isGivenKind(\T_INTERFACE)) {
            // cannot use Trait inside an interface
            return \false;
        }
        $useIndex = $tokens->getNextTokenOfKind($classOpenIndex, [[\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT]]);
        return null !== $useIndex && $useIndex < $classCloseIndex;
    }
}
