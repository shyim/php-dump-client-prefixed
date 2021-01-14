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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class MultilineCommentOpeningClosingFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace _PhpScoper3fe455fa007d;

/******
 * Multiline comment with arbitrary asterisks count
 ******/
/**\
 * Multiline comment that seems a DocBlock
 */
/**
 * DocBlock with arbitrary asterisk count at the end
 **/

EOT
)]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_COMMENT, \T_DOC_COMMENT]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $originalContent = $token->getContent();
            if (!$token->isGivenKind(\T_DOC_COMMENT) && !($token->isGivenKind(\T_COMMENT) && 0 === \strpos($originalContent, '/*'))) {
                continue;
            }
            $newContent = $originalContent;
            // Fix opening
            if ($token->isGivenKind(\T_COMMENT)) {
                $newContent = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/^\\/\\*{2,}(?!\\/)/', '/*', $newContent);
            }
            // Fix closing
            $newContent = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(?<!\\/)\\*{2,}\\/$/', '*/', $newContent);
            if ($newContent !== $originalContent) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), $newContent]);
            }
        }
    }
}
