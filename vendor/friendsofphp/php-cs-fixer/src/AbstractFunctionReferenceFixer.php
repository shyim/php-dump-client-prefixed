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
namespace _PhpScoper3fe455fa007d\PhpCsFixer;

use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
abstract class AbstractFunctionReferenceFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * Looks up Tokens sequence for suitable candidates and delivers boundaries information,
     * which can be supplied by other methods in this abstract class.
     *
     * @param string   $functionNameToSearch
     * @param int      $start
     * @param null|int $end
     *
     * @return null|int[] returns $functionName, $openParenthesis, $closeParenthesis packed into array
     */
    protected function find($functionNameToSearch, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $start = 0, $end = null)
    {
        // make interface consistent with findSequence
        $end = null === $end ? $tokens->count() : $end;
        // find raw sequence which we can analyse for context
        $candidateSequence = [[\T_STRING, $functionNameToSearch], '('];
        $matches = $tokens->findSequence($candidateSequence, $start, $end, \false);
        if (null === $matches) {
            // not found, simply return without further attempts
            return null;
        }
        // translate results for humans
        list($functionName, $openParenthesis) = \array_keys($matches);
        $functionsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $functionName)) {
            return $this->find($functionNameToSearch, $tokens, $openParenthesis, $end);
        }
        return [$functionName, $openParenthesis, $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis)];
    }
}
