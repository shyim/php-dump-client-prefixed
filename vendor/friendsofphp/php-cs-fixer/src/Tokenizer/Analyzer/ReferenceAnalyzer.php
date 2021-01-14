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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer;

use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class ReferenceAnalyzer
{
    /**
     * @param int $index
     *
     * @return bool
     */
    public function isReference(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if ($tokens[$index]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_RETURN_REF)) {
            return \true;
        }
        if (!$tokens[$index]->equals('&')) {
            return \false;
        }
        /** @var int $index */
        $index = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$index]->equalsAny(['=', [\T_AS], [\T_CALLABLE], [\T_DOUBLE_ARROW], [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT]])) {
            return \true;
        }
        if ($tokens[$index]->isGivenKind(\T_STRING)) {
            $index = $tokens->getPrevMeaningfulToken($index);
        }
        return $tokens[$index]->equalsAny(['(', ',', [\T_NS_SEPARATOR], [\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE]]);
    }
}
