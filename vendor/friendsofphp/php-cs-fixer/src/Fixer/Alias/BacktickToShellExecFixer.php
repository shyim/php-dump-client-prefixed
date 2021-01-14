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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Alias;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class BacktickToShellExecFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound('`');
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Converts backtick operators to `shell_exec` calls.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace _PhpScoper3fe455fa007d;

$plain = `ls -lah`;
$withVar = `ls -lah {$var1} {$var2} {$var3} {$var4[0]} {$var5->call()}`;

EOT
)], 'Conversion is done only when it is non risky, so when special chars like single-quotes, double-quotes and backticks are not used inside the command.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before EscapeImplicitBackslashesFixer, ExplicitStringVariableFixer, NativeFunctionInvocationFixer, SingleQuoteFixer.
     */
    public function getPriority()
    {
        return 2;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $backtickStarted = \false;
        $backtickTokens = [];
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->equals('`')) {
                if ($backtickStarted) {
                    $backtickTokens[$index] = $token;
                }
                continue;
            }
            $backtickTokens[$index] = $token;
            if ($backtickStarted) {
                $this->fixBackticks($tokens, $backtickTokens);
                $backtickTokens = [];
            }
            $backtickStarted = !$backtickStarted;
        }
    }
    /**
     * Override backtick code with corresponding double-quoted string.
     */
    private function fixBackticks(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $backtickTokens)
    {
        // Track indexes for final override
        \ksort($backtickTokens);
        $openingBacktickIndex = \key($backtickTokens);
        \end($backtickTokens);
        $closingBacktickIndex = \key($backtickTokens);
        // Strip enclosing backticks
        \array_shift($backtickTokens);
        \array_pop($backtickTokens);
        // Double-quoted strings are parsed differently if they contain
        // variables or not, so we need to build the new token array accordingly
        $count = \count($backtickTokens);
        $newTokens = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, 'shell_exec']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('(')];
        if (1 !== $count) {
            $newTokens[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('"');
        }
        foreach ($backtickTokens as $token) {
            if (!$token->isGivenKind(\T_ENCAPSED_AND_WHITESPACE)) {
                $newTokens[] = $token;
                continue;
            }
            $content = $token->getContent();
            // Escaping special chars depends on the context: too tricky
            if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/[`"\']/u', $content)) {
                return;
            }
            $kind = \T_ENCAPSED_AND_WHITESPACE;
            if (1 === $count) {
                $content = '"' . $content . '"';
                $kind = \T_CONSTANT_ENCAPSED_STRING;
            }
            $newTokens[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$kind, $content]);
        }
        if (1 !== $count) {
            $newTokens[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('"');
        }
        $newTokens[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(')');
        $tokens->overrideRange($openingBacktickIndex, $closingBacktickIndex, $newTokens);
    }
}
