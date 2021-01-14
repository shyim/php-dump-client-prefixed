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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Strict;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author SpacePossum
 */
final class DeclareStrictTypesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Force strict types declaration in all files. Requires PHP >= 7.0.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\n", new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70000))], null, 'Forcing strict types will stop non strict code from working.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BlankLineAfterOpeningTagFixer, DeclareEqualNormalizeFixer, HeaderCommentFixer.
     */
    public function getPriority()
    {
        return 2;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70000 && isset($tokens[0]) && $tokens[0]->isGivenKind(\T_OPEN_TAG);
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        // check if the declaration is already done
        $searchIndex = $tokens->getNextMeaningfulToken(0);
        if (null === $searchIndex) {
            $this->insertSequence($tokens);
            // declaration not found, insert one
            return;
        }
        $sequenceLocation = $tokens->findSequence([[\T_DECLARE, 'declare'], '(', [\T_STRING, 'strict_types'], '=', [\T_LNUMBER], ')'], $searchIndex, null, \false);
        if (null === $sequenceLocation) {
            $this->insertSequence($tokens);
            // declaration not found, insert one
            return;
        }
        $this->fixStrictTypesCasingAndValue($tokens, $sequenceLocation);
    }
    /**
     * @param array<int, Token> $sequence
     */
    private function fixStrictTypesCasingAndValue(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $sequence)
    {
        /** @var int $index */
        /** @var Token $token */
        foreach ($sequence as $index => $token) {
            if ($token->isGivenKind(\T_STRING)) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, \strtolower($token->getContent())]);
                continue;
            }
            if ($token->isGivenKind(\T_LNUMBER)) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_LNUMBER, '1']);
                break;
            }
        }
    }
    private function insertSequence(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $sequence = [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DECLARE, 'declare']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('('), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, 'strict_types']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('='), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_LNUMBER, '1']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(')'), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(';')];
        $endIndex = \count($sequence);
        $tokens->insertAt(1, $sequence);
        // start index of the sequence is always 1 here, 0 is always open tag
        // transform "<?php\n" to "<?php " if needed
        if (\false !== \strpos($tokens[0]->getContent(), "\n")) {
            $tokens[0] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$tokens[0]->getId(), \trim($tokens[0]->getContent()) . ' ']);
        }
        if ($endIndex === \count($tokens) - 1) {
            return;
            // no more tokens afters sequence, single_blank_line_at_eof might add a line
        }
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        if (!$tokens[1 + $endIndex]->isWhitespace()) {
            $tokens->insertAt(1 + $endIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnding]));
            return;
        }
        $content = $tokens[1 + $endIndex]->getContent();
        $tokens[1 + $endIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnding . \ltrim($content, " \t")]);
    }
}
