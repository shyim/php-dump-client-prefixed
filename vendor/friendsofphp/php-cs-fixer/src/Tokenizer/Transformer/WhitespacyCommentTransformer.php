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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Transformer;

use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\AbstractTransformer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * Move trailing whitespaces from comments and docs into following T_WHITESPACE token.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class WhitespacyCommentTransformer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 50000;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$token->isComment()) {
            return;
        }
        $content = $token->getContent();
        $trimmedContent = \rtrim($content);
        // nothing trimmed, nothing to do
        if ($content === $trimmedContent) {
            return;
        }
        $whitespaces = \substr($content, \strlen($trimmedContent));
        $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), $trimmedContent]);
        if (isset($tokens[$index + 1]) && $tokens[$index + 1]->isWhitespace()) {
            $tokens[$index + 1] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $whitespaces . $tokens[$index + 1]->getContent()]);
        } else {
            $tokens->insertAt($index + 1, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $whitespaces]));
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function getDeprecatedCustomTokens()
    {
        return [];
    }
}
