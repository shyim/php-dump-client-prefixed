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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Operator;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class ConcatSpaceFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    private $fixCallback;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if ('one' === $this->configuration['spacing']) {
            $this->fixCallback = 'fixConcatenationToSingleSpace';
        } else {
            $this->fixCallback = 'fixConcatenationToNoSpace';
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Concatenation should be spaced according configuration.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';\n", ['spacing' => 'none']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';\n", ['spacing' => 'one'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after SingleLineThrowFixer.
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
        return $tokens->isTokenKindFound('.');
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $callBack = $this->fixCallback;
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->equals('.')) {
                $this->{$callBack}($tokens, $index);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('spacing', 'Spacing to apply around concatenation operator.'))->setAllowedValues(['one', 'none'])->setDefault('none')->getOption()]);
    }
    /**
     * @param int $index index of concatenation '.' token
     */
    private function fixConcatenationToNoSpace(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];
        if (!$prevNonWhitespaceToken->isGivenKind([\T_LNUMBER, \T_COMMENT, \T_DOC_COMMENT]) || '/*' === \substr($prevNonWhitespaceToken->getContent(), 0, 2)) {
            $tokens->removeLeadingWhitespace($index, " \t");
        }
        if (!$tokens[$tokens->getNextNonWhitespace($index)]->isGivenKind([\T_LNUMBER, \T_COMMENT, \T_DOC_COMMENT])) {
            $tokens->removeTrailingWhitespace($index, " \t");
        }
    }
    /**
     * @param int $index index of concatenation '.' token
     */
    private function fixConcatenationToSingleSpace(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $this->fixWhiteSpaceAroundConcatToken($tokens, $index, 1);
        $this->fixWhiteSpaceAroundConcatToken($tokens, $index, -1);
    }
    /**
     * @param int $index  index of concatenation '.' token
     * @param int $offset 1 or -1
     */
    private function fixWhiteSpaceAroundConcatToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $offset)
    {
        $offsetIndex = $index + $offset;
        if (!$tokens[$offsetIndex]->isWhitespace()) {
            $tokens->insertAt($index + (1 === $offset ?: 0), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
            return;
        }
        if (\false !== \strpos($tokens[$offsetIndex]->getContent(), "\n")) {
            return;
        }
        if ($tokens[$index + $offset * 2]->isComment()) {
            return;
        }
        $tokens[$offsetIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
    }
}
