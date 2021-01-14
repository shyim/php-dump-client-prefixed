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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\StringNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gregor Harlan
 */
final class NoTrailingWhitespaceInStringFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_CONSTANT_ENCAPSED_STRING, \T_ENCAPSED_AND_WHITESPACE, \T_INLINE_HTML]);
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
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('There must be no trailing whitespace in strings.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php \$a = '  \n    foo \n';\n")], null, 'Changing the whitespaces in strings might affect string comparisons and outputs.');
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = $tokens->count() - 1, $last = \true; $index >= 0; --$index, $last = \false) {
            /** @var Token $token */
            $token = $tokens[$index];
            if (!$token->isGivenKind([\T_CONSTANT_ENCAPSED_STRING, \T_ENCAPSED_AND_WHITESPACE, \T_INLINE_HTML])) {
                continue;
            }
            $isInlineHtml = $token->isGivenKind(\T_INLINE_HTML);
            $regex = $isInlineHtml && $last ? '/\\h+(?=\\R|$)/' : '/\\h+(?=\\R)/';
            $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace($regex, '', $token->getContent());
            if ($token->getContent() === $content) {
                continue;
            }
            if (!$isInlineHtml || 0 === $index) {
                $this->updateContent($tokens, $index, $content);
                continue;
            }
            $prev = $index - 1;
            if ($tokens[$prev]->equals([\T_CLOSE_TAG, '?>']) && \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/^\\R/', $content, $match)) {
                $tokens[$prev] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_CLOSE_TAG, $tokens[$prev]->getContent() . $match[0]]);
                $content = \substr($content, \strlen($match[0]));
                $content = \false === $content ? '' : $content;
            }
            $this->updateContent($tokens, $index, $content);
        }
    }
    /**
     * @param int    $index
     * @param string $content
     */
    private function updateContent(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $content)
    {
        if ('' === $content) {
            $tokens->clearAt($index);
            return;
        }
        $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), $content]);
    }
}
