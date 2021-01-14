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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ReturnNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class SimplifiedNullReturnFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('A return statement wishing to return `void` should not return `null`.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php return null;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'EOT'
<?php

namespace _PhpScoper3fe455fa007d;

function foo()
{
    return null;
}
function bar() : int
{
    return null;
}
function baz() : ?int
{
    return null;
}
function xyz() : void
{
    return null;
}

EOT
, new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70100))]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoUselessReturnFixer, VoidReturnFixer.
     */
    public function getPriority()
    {
        return 16;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_RETURN);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_RETURN)) {
                continue;
            }
            if ($this->needFixing($tokens, $index)) {
                $this->clear($tokens, $index);
            }
        }
    }
    /**
     * Clear the return statement located at a given index.
     *
     * @param int $index
     */
    private function clear(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        while (!$tokens[++$index]->equals(';')) {
            if ($this->shouldClearToken($tokens, $index)) {
                $tokens->clearAt($index);
            }
        }
    }
    /**
     * Does the return statement located at a given index need fixing?
     *
     * @param int $index
     *
     * @return bool
     */
    private function needFixing(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if ($this->isStrictOrNullableReturnTypeFunction($tokens, $index)) {
            return \false;
        }
        $content = '';
        while (!$tokens[$index]->equals(';')) {
            $index = $tokens->getNextMeaningfulToken($index);
            $content .= $tokens[$index]->getContent();
        }
        $content = \ltrim($content, '(');
        $content = \rtrim($content, ');');
        return 'null' === \strtolower($content);
    }
    /**
     * Is the return within a function with a non-void or nullable return type?
     *
     * @param int $returnIndex Current return token index
     *
     * @return bool
     */
    private function isStrictOrNullableReturnTypeFunction(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $returnIndex)
    {
        $functionIndex = $returnIndex;
        do {
            $functionIndex = $tokens->getPrevTokenOfKind($functionIndex, [[\T_FUNCTION]]);
            if (null === $functionIndex) {
                return \false;
            }
            $openingCurlyBraceIndex = $tokens->getNextTokenOfKind($functionIndex, ['{']);
            $closingCurlyBraceIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $openingCurlyBraceIndex);
        } while ($closingCurlyBraceIndex < $returnIndex);
        $possibleVoidIndex = $tokens->getPrevMeaningfulToken($openingCurlyBraceIndex);
        $isStrictReturnType = $tokens[$possibleVoidIndex]->isGivenKind(\T_STRING) && 'void' !== $tokens[$possibleVoidIndex]->getContent();
        $nullableTypeIndex = $tokens->getNextTokenOfKind($functionIndex, [[\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE]]);
        $isNullableReturnType = null !== $nullableTypeIndex && $nullableTypeIndex < $openingCurlyBraceIndex;
        return $isStrictReturnType || $isNullableReturnType;
    }
    /**
     * Should we clear the specific token?
     *
     * If the token is a comment, or is whitespace that is immediately before a
     * comment, then we'll leave it alone.
     *
     * @param int $index
     *
     * @return bool
     */
    private function shouldClearToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $token = $tokens[$index];
        return !$token->isComment() && !($token->isWhitespace() && $tokens[$index + 1]->isComment());
    }
}
