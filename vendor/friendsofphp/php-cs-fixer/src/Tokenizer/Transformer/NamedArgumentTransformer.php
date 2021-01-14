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
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * Transform named argument tokens.
 *
 * @author SpacePossum
 *
 * @internal
 */
final class NamedArgumentTransformer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // needs to run after TypeColonTransformer
        return -15;
    }
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 80000;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$tokens[$index]->equals(':')) {
            return;
        }
        $stringIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$stringIndex]->isGivenKind(\T_STRING)) {
            return;
        }
        $preStringIndex = $tokens->getPrevMeaningfulToken($stringIndex);
        // if equals any [';', '{', '}', [T_OPEN_TAG]] than it is a goto label
        // if equals ')' than likely it is a type colon, but sure not a name argument
        // if equals '?' than it is part of ternary statement
        if (!$tokens[$preStringIndex]->equalsAny([',', '('])) {
            return;
        }
        $tokens[$stringIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NAMED_ARGUMENT_NAME, $tokens[$stringIndex]->getContent()]);
        $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NAMED_ARGUMENT_COLON, ':']);
    }
    /**
     * {@inheritdoc}
     */
    protected function getDeprecatedCustomTokens()
    {
        return [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NAMED_ARGUMENT_COLON, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NAMED_ARGUMENT_NAME];
    }
}
