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
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class HeredocToNowdocFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Convert `heredoc` to `nowdoc` where possible.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample(<<<'EOF'
<?php

namespace _PhpScoper3fe455fa007d;

$a = <<<TEST
Foo
TEST
;

EOF
)]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after EscapeImplicitBackslashesFixer.
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
        return $tokens->isTokenKindFound(\T_START_HEREDOC);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_START_HEREDOC) || \false !== \strpos($token->getContent(), "'")) {
                continue;
            }
            if ($tokens[$index + 1]->isGivenKind(\T_END_HEREDOC)) {
                $tokens[$index] = $this->convertToNowdoc($token);
                continue;
            }
            if (!$tokens[$index + 1]->isGivenKind(\T_ENCAPSED_AND_WHITESPACE) || !$tokens[$index + 2]->isGivenKind(\T_END_HEREDOC)) {
                continue;
            }
            $content = $tokens[$index + 1]->getContent();
            // regex: odd number of backslashes, not followed by dollar
            if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/(?<!\\\\)(?:\\\\{2})*\\\\(?![$\\\\])/', $content)) {
                continue;
            }
            $tokens[$index] = $this->convertToNowdoc($token);
            $content = \str_replace(['\\\\', '\\$'], ['\\', '$'], $content);
            $tokens[$index + 1] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$tokens[$index + 1]->getId(), $content]);
        }
    }
    /**
     * Transforms the heredoc start token to nowdoc notation.
     *
     * @return Token
     */
    private function convertToNowdoc(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token)
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/^([Bb]?<<<)(\\h*)"?([^\\s"]+)"?/', '$1$2\'$3\'', $token->getContent())]);
    }
}
