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
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 */
final class SimpleToComplexStringVariableFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace _PhpScoper3fe455fa007d;

$name = 'World';
echo "Hello {$name}!";

EOT
), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace _PhpScoper3fe455fa007d;

$name = 'World';
echo <<<TEST
Hello {$name}!
TEST
;

EOT
)], "Doesn't touch implicit variables. Works together nicely with `explicit_string_variable`.");
    }
    /**
     * {@inheritdoc}
     *
     * Must run after ExplicitStringVariableFixer.
     */
    public function getPriority()
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_DOLLAR_OPEN_CURLY_BRACES);
    }
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = \count($tokens) - 3; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_DOLLAR_OPEN_CURLY_BRACES)) {
                continue;
            }
            $varnameToken = $tokens[$index + 1];
            if (!$varnameToken->isGivenKind(\T_STRING_VARNAME)) {
                continue;
            }
            $dollarCloseToken = $tokens[$index + 2];
            if (!$dollarCloseToken->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DOLLAR_CLOSE_CURLY_BRACES)) {
                continue;
            }
            $tokenOfStringBeforeToken = $tokens[$index - 1];
            $stringContent = $tokenOfStringBeforeToken->getContent();
            if ('$' === \substr($stringContent, -1) && '\\$' !== \substr($stringContent, -2)) {
                $newContent = \substr($stringContent, 0, -1) . '\\$';
                $tokenOfStringBeforeToken = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_ENCAPSED_AND_WHITESPACE, $newContent]);
            }
            $tokens->overrideRange($index - 1, $index + 2, [$tokenOfStringBeforeToken, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_CURLY_OPEN, '{']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_VARIABLE, '$' . $varnameToken->getContent()]), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CURLY_CLOSE, '}'])]);
        }
    }
}
