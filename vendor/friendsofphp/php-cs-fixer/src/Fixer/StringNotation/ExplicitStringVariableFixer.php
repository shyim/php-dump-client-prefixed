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
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class ExplicitStringVariableFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace _PhpScoper3fe455fa007d;

$a = "My name is {$name} !";
$b = "I live in {$state->country} !";
$c = "I have {$farm[0]} chickens !";

EOT
)], 'The reasoning behind this rule is the following:' . "\n" . '- When there are two valid ways of doing the same thing, using both is confusing, there should be a coding standard to follow' . "\n" . '- PHP manual marks `"$var"` syntax as implicit and `"${var}"` syntax as explicit: explicit code should always be preferred' . "\n" . '- Explicit syntax allows word concatenation inside strings, e.g. `"${var}IsAVar"`, implicit doesn\'t' . "\n" . '- Explicit syntax is easier to detect for IDE/editors and therefore has colors/highlight with higher contrast, which is easier to read' . "\n" . 'Backtick operator is skipped because it is harder to handle; you can use `backtick_to_shell_exec` fixer to normalize backticks to strings');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before SimpleToComplexStringVariableFixer.
     * Must run after BacktickToShellExecFixer.
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_VARIABLE);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $backtickStarted = \false;
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            $token = $tokens[$index];
            if ($token->equals('`')) {
                $backtickStarted = !$backtickStarted;
                continue;
            }
            if ($backtickStarted || !$token->isGivenKind(\T_VARIABLE)) {
                continue;
            }
            $prevToken = $tokens[$index - 1];
            if (!$this->isStringPartToken($prevToken)) {
                continue;
            }
            $distinctVariableIndex = $index;
            $variableTokens = [$distinctVariableIndex => ['tokens' => [$index => $token], 'firstVariableTokenIndex' => $index, 'lastVariableTokenIndex' => $index]];
            $nextIndex = $index + 1;
            $squareBracketCount = 0;
            while (!$this->isStringPartToken($tokens[$nextIndex])) {
                if ($tokens[$nextIndex]->isGivenKind(\T_CURLY_OPEN)) {
                    $nextIndex = $tokens->getNextTokenOfKind($nextIndex, [[\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CURLY_CLOSE]]);
                } elseif ($tokens[$nextIndex]->isGivenKind(\T_VARIABLE) && 1 !== $squareBracketCount) {
                    $distinctVariableIndex = $nextIndex;
                    $variableTokens[$distinctVariableIndex] = ['tokens' => [$nextIndex => $tokens[$nextIndex]], 'firstVariableTokenIndex' => $nextIndex, 'lastVariableTokenIndex' => $nextIndex];
                } else {
                    $variableTokens[$distinctVariableIndex]['tokens'][$nextIndex] = $tokens[$nextIndex];
                    $variableTokens[$distinctVariableIndex]['lastVariableTokenIndex'] = $nextIndex;
                    if ($tokens[$nextIndex]->equalsAny(['[', ']'])) {
                        ++$squareBracketCount;
                    }
                }
                ++$nextIndex;
            }
            \krsort($variableTokens, \SORT_NUMERIC);
            foreach ($variableTokens as $distinctVariableSet) {
                if (1 === \count($distinctVariableSet['tokens'])) {
                    $singleVariableIndex = \key($distinctVariableSet['tokens']);
                    $singleVariableToken = \current($distinctVariableSet['tokens']);
                    $tokens->overrideRange($singleVariableIndex, $singleVariableIndex, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOLLAR_OPEN_CURLY_BRACES, '${']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING_VARNAME, \substr($singleVariableToken->getContent(), 1)]), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DOLLAR_CLOSE_CURLY_BRACES, '}'])]);
                } else {
                    foreach ($distinctVariableSet['tokens'] as $variablePartIndex => $variablePartToken) {
                        if ($variablePartToken->isGivenKind(\T_NUM_STRING)) {
                            $tokens[$variablePartIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_LNUMBER, $variablePartToken->getContent()]);
                            continue;
                        }
                        if ($variablePartToken->isGivenKind(\T_STRING) && $tokens[$variablePartIndex + 1]->equals(']')) {
                            $tokens[$variablePartIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_CONSTANT_ENCAPSED_STRING, "'" . $variablePartToken->getContent() . "'"]);
                        }
                    }
                    $tokens->insertAt($distinctVariableSet['lastVariableTokenIndex'] + 1, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CURLY_CLOSE, '}']));
                    $tokens->insertAt($distinctVariableSet['firstVariableTokenIndex'], new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_CURLY_OPEN, '{']));
                }
            }
        }
    }
    /**
     * Check if token is a part of a string.
     *
     * @param Token $token The token to check
     *
     * @return bool
     */
    private function isStringPartToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token)
    {
        return $token->isGivenKind(\T_ENCAPSED_AND_WHITESPACE) || $token->isGivenKind(\T_START_HEREDOC) || '"' === $token->getContent() || 'b"' === \strtolower($token->getContent());
    }
}
