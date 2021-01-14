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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ControlStructure;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Eddilbert Macharia <edd.cowan@gmail.com>
 */
final class NoAlternativeSyntaxFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Replace control structure alternative syntax to use braces.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nif(true):echo 't';else:echo 'f';endif;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nwhile(true):echo 'red';endwhile;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfor(;;):echo 'xc';endfor;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nforeach(array('a') as \$item):echo 'xc';endforeach;\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->hasAlternativeSyntax();
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, ElseifFixer, NoSuperfluousElseifFixer, NoUselessElseFixer, SwitchContinueToBreakFixer.
     */
    public function getPriority()
    {
        return 26;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            $this->fixElseif($index, $token, $tokens);
            $this->fixElse($index, $token, $tokens);
            $this->fixOpenCloseControls($index, $token, $tokens);
        }
    }
    private function findParenthesisEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $structureTokenIndex)
    {
        $nextIndex = $tokens->getNextMeaningfulToken($structureTokenIndex);
        $nextToken = $tokens[$nextIndex];
        // return if next token is not opening parenthesis
        if (!$nextToken->equals('(')) {
            return $structureTokenIndex;
        }
        return $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }
    /**
     * Handle both extremes of the control structures.
     * e.g. if(): or endif;.
     *
     * @param int    $index  the index of the token being processed
     * @param Token  $token  the token being processed
     * @param Tokens $tokens the collection of tokens
     */
    private function fixOpenCloseControls($index, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if ($token->isGivenKind([\T_IF, \T_FOREACH, \T_WHILE, \T_FOR, \T_SWITCH, \T_DECLARE])) {
            $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $closeIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            $afterParenthesisIndex = $tokens->getNextMeaningfulToken($closeIndex);
            $afterParenthesis = $tokens[$afterParenthesisIndex];
            if (!$afterParenthesis->equals(':')) {
                return;
            }
            $items = [];
            if (!$tokens[$afterParenthesisIndex - 1]->isWhitespace()) {
                $items[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
            }
            $items[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('{');
            if (!$tokens[$afterParenthesisIndex + 1]->isWhitespace()) {
                $items[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
            }
            $tokens->clearAt($afterParenthesisIndex);
            $tokens->insertAt($afterParenthesisIndex, $items);
        }
        if (!$token->isGivenKind([\T_ENDIF, \T_ENDFOREACH, \T_ENDWHILE, \T_ENDFOR, \T_ENDSWITCH, \T_ENDDECLARE])) {
            return;
        }
        $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
        $nextToken = $tokens[$nextTokenIndex];
        $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('}');
        if ($nextToken->equals(';')) {
            $tokens->clearAt($nextTokenIndex);
        }
    }
    /**
     * Handle the else: cases.
     *
     * @param int    $index  the index of the token being processed
     * @param Token  $token  the token being processed
     * @param Tokens $tokens the collection of tokens
     */
    private function fixElse($index, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if (!$token->isGivenKind(\T_ELSE)) {
            return;
        }
        $tokenAfterElseIndex = $tokens->getNextMeaningfulToken($index);
        $tokenAfterElse = $tokens[$tokenAfterElseIndex];
        if (!$tokenAfterElse->equals(':')) {
            return;
        }
        $this->addBraces($tokens, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_ELSE, 'else']), $index, $tokenAfterElseIndex);
    }
    /**
     * Handle the elsif(): cases.
     *
     * @param int    $index  the index of the token being processed
     * @param Token  $token  the token being processed
     * @param Tokens $tokens the collection of tokens
     */
    private function fixElseif($index, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if (!$token->isGivenKind(\T_ELSEIF)) {
            return;
        }
        $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
        $tokenAfterParenthesisIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
        $tokenAfterParenthesis = $tokens[$tokenAfterParenthesisIndex];
        if (!$tokenAfterParenthesis->equals(':')) {
            return;
        }
        $this->addBraces($tokens, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_ELSEIF, 'elseif']), $index, $tokenAfterParenthesisIndex);
    }
    /**
     * Add opening and closing braces to the else: and elseif: cases.
     *
     * @param Tokens $tokens     the tokens collection
     * @param Token  $token      the current token
     * @param int    $index      the current token index
     * @param int    $colonIndex the index of the colon
     */
    private function addBraces(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, $index, $colonIndex)
    {
        $items = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('}'), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), $token];
        if (!$tokens[$index + 1]->isWhitespace()) {
            $items[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
        }
        $tokens->clearAt($index);
        $tokens->insertAt($index, $items);
        // increment the position of the colon by number of items inserted
        $colonIndex += \count($items);
        $items = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('{')];
        if (!$tokens[$colonIndex + 1]->isWhitespace()) {
            $items[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
        }
        $tokens->clearAt($colonIndex);
        $tokens->insertAt($colonIndex, $items);
    }
}
