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
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options;
/**
 * Fixer for rule defined in PSR2 ¶5.2.
 */
final class NoBreakCommentFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('There must be a comment when fall-through is intentional in a non-empty case body.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
switch ($foo) {
    case 1:
        foo();
    case 2:
        bar();
        // no break
        break;
    case 3:
        baz();
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
switch ($foo) {
    case 1:
        foo();
    case 2:
        foo();
}
', ['comment_text' => 'some comment'])], 'Adds a "no break" comment before fall-through cases, and removes it if there is no fall-through.');
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_CASE, \T_DEFAULT]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after NoUselessElseFixer.
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('comment_text', 'The text to use in the added comment and to detect it.'))->setAllowedTypes(['string'])->setAllowedValues([static function ($value) {
            if (\is_string($value) && \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $value)) {
                throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException('The comment text must not contain new lines.');
            }
            return \true;
        }])->setNormalizer(static function (\_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options $options, $value) {
            return \rtrim($value);
        })->setDefault('no break')->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($position = \count($tokens) - 1; $position >= 0; --$position) {
            if ($tokens[$position]->isGivenKind([\T_CASE, \T_DEFAULT])) {
                $this->fixCase($tokens, $position);
            }
        }
    }
    /**
     * @param int $casePosition
     */
    private function fixCase(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $casePosition)
    {
        $empty = \true;
        $fallThrough = \true;
        $commentPosition = null;
        $caseColonIndex = $tokens->getNextTokenOfKind($casePosition, [':', ';']);
        for ($i = $caseColonIndex + 1, $max = \count($tokens); $i < $max; ++$i) {
            if ($tokens[$i]->isGivenKind([\T_SWITCH, \T_IF, \T_ELSE, \T_ELSEIF, \T_FOR, \T_FOREACH, \T_WHILE, \T_DO, \T_FUNCTION, \T_CLASS])) {
                $empty = \false;
                $i = $this->getStructureEnd($tokens, $i);
                continue;
            }
            if ($tokens[$i]->isGivenKind([\T_BREAK, \T_CONTINUE, \T_RETURN, \T_EXIT, \T_GOTO])) {
                $fallThrough = \false;
                continue;
            }
            if ($tokens[$i]->isGivenKind([\T_THROW])) {
                $previousIndex = $tokens->getPrevMeaningfulToken($i);
                if ($previousIndex === $caseColonIndex || $tokens[$previousIndex]->equalsAny(['{', ';', '}', [\T_OPEN_TAG]])) {
                    $fallThrough = \false;
                }
                continue;
            }
            if ($tokens[$i]->equals('}') || $tokens[$i]->isGivenKind(\T_ENDSWITCH)) {
                if (null !== $commentPosition) {
                    $this->removeComment($tokens, $commentPosition);
                }
                break;
            }
            if ($this->isNoBreakComment($tokens[$i])) {
                $commentPosition = $i;
                continue;
            }
            if ($tokens[$i]->isGivenKind([\T_CASE, \T_DEFAULT])) {
                if (!$empty && $fallThrough) {
                    if (null !== $commentPosition && $tokens->getPrevNonWhitespace($i) !== $commentPosition) {
                        $this->removeComment($tokens, $commentPosition);
                        $commentPosition = null;
                    }
                    if (null === $commentPosition) {
                        $this->insertCommentAt($tokens, $i);
                    } else {
                        $text = $this->configuration['comment_text'];
                        $tokens[$commentPosition] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$tokens[$commentPosition]->getId(), \str_ireplace($text, $text, $tokens[$commentPosition]->getContent())]);
                        $this->ensureNewLineAt($tokens, $commentPosition);
                    }
                } elseif (null !== $commentPosition) {
                    $this->removeComment($tokens, $commentPosition);
                }
                break;
            }
            if (!$tokens[$i]->isGivenKind([\T_COMMENT, \T_WHITESPACE])) {
                $empty = \false;
            }
        }
    }
    /**
     * @return bool
     */
    private function isNoBreakComment(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token)
    {
        if (!$token->isComment()) {
            return \false;
        }
        $text = \preg_quote($this->configuration['comment_text'], '~');
        return 1 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match("~^((//|#)\\s*{$text}\\s*)|(/\\*\\*?\\s*{$text}\\s*\\*/)\$~i", $token->getContent());
    }
    /**
     * @param int $casePosition
     */
    private function insertCommentAt(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $casePosition)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $newlinePosition = $this->ensureNewLineAt($tokens, $casePosition);
        $newlineToken = $tokens[$newlinePosition];
        $nbNewlines = \substr_count($newlineToken->getContent(), $lineEnding);
        if ($newlineToken->isGivenKind(\T_OPEN_TAG) && \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $newlineToken->getContent())) {
            ++$nbNewlines;
        } elseif ($tokens[$newlinePosition - 1]->isGivenKind(\T_OPEN_TAG) && \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $tokens[$newlinePosition - 1]->getContent())) {
            ++$nbNewlines;
            if (!\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $newlineToken->getContent())) {
                $tokens[$newlinePosition] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$newlineToken->getId(), $lineEnding . $newlineToken->getContent()]);
            }
        }
        if ($nbNewlines > 1) {
            \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/^(.*?)(\\R\\h*)$/s', $newlineToken->getContent(), $matches);
            $indent = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $newlinePosition - 1);
            $tokens[$newlinePosition] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$newlineToken->getId(), $matches[1] . $lineEnding . $indent]);
            $tokens->insertAt(++$newlinePosition, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $matches[2]]));
        }
        $tokens->insertAt($newlinePosition, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_COMMENT, '// ' . $this->configuration['comment_text']]));
        $this->ensureNewLineAt($tokens, $newlinePosition);
    }
    /**
     * @param int $position
     *
     * @return int The newline token position
     */
    private function ensureNewLineAt(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $content = $lineEnding . \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $position);
        $whitespaceToken = $tokens[$position - 1];
        if (!$whitespaceToken->isGivenKind(\T_WHITESPACE)) {
            if ($whitespaceToken->isGivenKind(\T_OPEN_TAG)) {
                $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\R/', '', $content);
                if (!\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $whitespaceToken->getContent())) {
                    $tokens[$position - 1] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_OPEN_TAG, \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\s+$/', $lineEnding, $whitespaceToken->getContent())]);
                }
            }
            if ('' !== $content) {
                $tokens->insertAt($position, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]));
                return $position;
            }
            return $position - 1;
        }
        if ($tokens[$position - 2]->isGivenKind(\T_OPEN_TAG) && \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $tokens[$position - 2]->getContent())) {
            $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/^\\R/', '', $content);
        }
        if (!\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $whitespaceToken->getContent())) {
            $tokens[$position - 1] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]);
        }
        return $position - 1;
    }
    /**
     * @param int $commentPosition
     */
    private function removeComment(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $commentPosition)
    {
        if ($tokens[$tokens->getPrevNonWhitespace($commentPosition)]->isGivenKind(\T_OPEN_TAG)) {
            $whitespacePosition = $commentPosition + 1;
            $regex = '/^\\R\\h*/';
        } else {
            $whitespacePosition = $commentPosition - 1;
            $regex = '/\\R\\h*$/';
        }
        $whitespaceToken = $tokens[$whitespacePosition];
        if ($whitespaceToken->isGivenKind(\T_WHITESPACE)) {
            $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace($regex, '', $whitespaceToken->getContent());
            if ('' !== $content) {
                $tokens[$whitespacePosition] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]);
            } else {
                $tokens->clearAt($whitespacePosition);
            }
        }
        $tokens->clearTokenAndMergeSurroundingWhitespace($commentPosition);
    }
    /**
     * @param int $position
     *
     * @return int
     */
    private function getStructureEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $initialToken = $tokens[$position];
        if ($initialToken->isGivenKind([\T_FOR, \T_FOREACH, \T_WHILE, \T_IF, \T_ELSEIF, \T_SWITCH, \T_FUNCTION])) {
            $position = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $tokens->getNextTokenOfKind($position, ['(']));
        } elseif ($initialToken->isGivenKind(\T_CLASS)) {
            $openParenthesisPosition = $tokens->getNextMeaningfulToken($position);
            if ('(' === $tokens[$openParenthesisPosition]->getContent()) {
                $position = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesisPosition);
            }
        }
        $position = $tokens->getNextMeaningfulToken($position);
        if ('{' !== $tokens[$position]->getContent()) {
            return $tokens->getNextTokenOfKind($position, [';']);
        }
        $position = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $position);
        if ($initialToken->isGivenKind(\T_DO)) {
            $position = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $tokens->getNextTokenOfKind($position, ['(']));
            return $tokens->getNextTokenOfKind($position, [';']);
        }
        return $position;
    }
}