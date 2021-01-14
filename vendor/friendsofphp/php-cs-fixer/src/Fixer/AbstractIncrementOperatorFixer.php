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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
abstract class AbstractIncrementOperatorFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * @param int $index
     *
     * @return int
     */
    protected final function findStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getPrevMeaningfulToken($index);
            $token = $tokens[$index];
            $blockType = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::detectBlockType($token);
            if (null !== $blockType && !$blockType['isStart']) {
                $index = $tokens->findBlockStart($blockType['type'], $index);
                $token = $tokens[$index];
            }
        } while (!$token->equalsAny(['$', [\T_VARIABLE]]));
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        $prevToken = $tokens[$prevIndex];
        if ($prevToken->equals('$')) {
            return $this->findStart($tokens, $index);
        }
        if ($prevToken->isGivenKind(\T_OBJECT_OPERATOR)) {
            return $this->findStart($tokens, $prevIndex);
        }
        if ($prevToken->isGivenKind(\T_PAAMAYIM_NEKUDOTAYIM)) {
            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if (!$tokens[$prevPrevIndex]->isGivenKind([\T_STATIC, \T_STRING])) {
                return $this->findStart($tokens, $prevIndex);
            }
            $index = $tokens->getTokenNotOfKindsSibling($prevIndex, -1, [\T_NS_SEPARATOR, \T_STATIC, \T_STRING]);
            $index = $tokens->getNextMeaningfulToken($index);
        }
        return $index;
    }
}