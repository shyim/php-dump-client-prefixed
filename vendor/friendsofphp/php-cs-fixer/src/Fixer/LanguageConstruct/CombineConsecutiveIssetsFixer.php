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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\LanguageConstruct;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class CombineConsecutiveIssetsFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Using `isset($var) &&` multiple times should be done in one call.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = isset(\$a) && isset(\$b);\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, NoSpacesInsideParenthesisFixer, NoTrailingWhitespaceFixer, NoWhitespaceInBlankLineFixer.
     */
    public function getPriority()
    {
        return 3;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_ISSET, \T_BOOLEAN_AND]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokenCount = $tokens->count();
        for ($index = 1; $index < $tokenCount; ++$index) {
            if (!$tokens[$index]->isGivenKind(\T_ISSET) || !$tokens[$tokens->getPrevMeaningfulToken($index)]->equalsAny(['(', '{', ';', '=', [\T_OPEN_TAG], [\T_BOOLEAN_AND], [\T_BOOLEAN_OR]])) {
                continue;
            }
            $issetInfo = $this->getIssetInfo($tokens, $index);
            $issetCloseBraceIndex = \end($issetInfo);
            // ')' token
            $insertLocation = \prev($issetInfo) + 1;
            // one index after the previous meaningful of ')'
            $booleanAndTokenIndex = $tokens->getNextMeaningfulToken($issetCloseBraceIndex);
            while ($tokens[$booleanAndTokenIndex]->isGivenKind(\T_BOOLEAN_AND)) {
                $issetIndex = $tokens->getNextMeaningfulToken($booleanAndTokenIndex);
                if (!$tokens[$issetIndex]->isGivenKind(\T_ISSET)) {
                    $index = $issetIndex;
                    break;
                }
                // fetch info about the 'isset' statement that we're merging
                $nextIssetInfo = $this->getIssetInfo($tokens, $issetIndex);
                $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken(\end($nextIssetInfo));
                $nextMeaningfulToken = $tokens[$nextMeaningfulTokenIndex];
                if (!$nextMeaningfulToken->equalsAny([')', '}', ';', [\T_CLOSE_TAG], [\T_BOOLEAN_AND], [\T_BOOLEAN_OR]])) {
                    $index = $nextMeaningfulTokenIndex;
                    break;
                }
                // clone what we want to move, do not clone '(' and ')' of the 'isset' statement we're merging
                $clones = $this->getTokenClones($tokens, \array_slice($nextIssetInfo, 1, -1));
                // clean up no the tokens of the 'isset' statement we're merging
                $this->clearTokens($tokens, \array_merge($nextIssetInfo, [$issetIndex, $booleanAndTokenIndex]));
                // insert the tokens to create the new statement
                \array_unshift($clones, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(','), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
                $tokens->insertAt($insertLocation, $clones);
                // correct some counts and offset based on # of tokens inserted
                $numberOfTokensInserted = \count($clones);
                $tokenCount += $numberOfTokensInserted;
                $issetCloseBraceIndex += $numberOfTokensInserted;
                $insertLocation += $numberOfTokensInserted;
                $booleanAndTokenIndex = $tokens->getNextMeaningfulToken($issetCloseBraceIndex);
            }
        }
    }
    /**
     * @param int[] $indexes
     */
    private function clearTokens(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $indexes)
    {
        foreach ($indexes as $index) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }
    /**
     * @param int $index of T_ISSET
     *
     * @return int[] indexes of meaningful tokens belonging to the isset statement
     */
    private function getIssetInfo(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextMeaningfulToken($index);
        $braceOpenCount = 1;
        $meaningfulTokenIndexes = [$openIndex];
        for ($i = $openIndex + 1;; ++$i) {
            if ($tokens[$i]->isWhitespace() || $tokens[$i]->isComment()) {
                continue;
            }
            $meaningfulTokenIndexes[] = $i;
            if ($tokens[$i]->equals(')')) {
                --$braceOpenCount;
                if (0 === $braceOpenCount) {
                    break;
                }
            } elseif ($tokens[$i]->equals('(')) {
                ++$braceOpenCount;
            }
        }
        return $meaningfulTokenIndexes;
    }
    /**
     * @param int[] $indexes
     *
     * @return Token[]
     */
    private function getTokenClones(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $indexes)
    {
        $clones = [];
        foreach ($indexes as $i) {
            $clones[] = clone $tokens[$i];
        }
        return $clones;
    }
}
