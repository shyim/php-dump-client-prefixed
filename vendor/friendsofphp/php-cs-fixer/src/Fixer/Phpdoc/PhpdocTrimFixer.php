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
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class PhpdocTrimFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('PHPDoc should start and end with content, excluding the very first and last line of the docblocks.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 *
 * Foo must be final class.
 *
 *
 */
final class Foo {}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, GeneralPhpdocAnnotationRemoveFixer, PhpUnitTestAnnotationFixer, PhpdocIndentFixer, PhpdocNoAccessFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocOrderFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return -5;
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
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $content = $token->getContent();
            $content = $this->fixStart($content);
            // we need re-parse the docblock after fixing the start before
            // fixing the end in order for the lines to be correctly indexed
            $content = $this->fixEnd($content);
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $content]);
        }
    }
    /**
     * Make sure the first useful line starts immediately after the first line.
     *
     * @param string $content
     *
     * @return string
     */
    private function fixStart($content)
    {
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('~
                (^/\\*\\*)            # DocComment begin
                (?:
                    \\R\\h*(?:\\*\\h*)? # lines without useful content
                    (?!\\R\\h*\\*/)    # not followed by a DocComment end
                )+
                (\\R\\h*(?:\\*\\h*)?\\S) # first line with useful content
            ~x', '$1$2', $content);
    }
    /**
     * Make sure the last useful line is immediately before the final line.
     *
     * @param string $content
     *
     * @return string
     */
    private function fixEnd($content)
    {
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('~
                (\\R\\h*(?:\\*\\h*)?\\S.*?) # last line with useful content
                (?:
                    (?<!/\\*\\*)         # not preceded by a DocComment start
                    \\R\\h*(?:\\*\\h*)?    # lines without useful content
                )+
                (\\R\\h*\\*/$)            # DocComment end
            ~xu', '$1$2', $content);
    }
}
