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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ListNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class ListSyntaxFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    private $candidateTokenKind;
    /**
     * Use 'syntax' => 'long'|'short'.
     *
     * @param null|array<string, string> $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN : \T_LIST;
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\n[\$sample] = \$array;\n", new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70100)), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nlist(\$sample) = \$array;\n", new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70100), ['syntax' => 'short'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, TernaryOperatorSpacesFixer.
     */
    public function getPriority()
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70100 && $tokens->isTokenKindFound($this->candidateTokenKind);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                if (\T_LIST === $this->candidateTokenKind) {
                    $this->fixToShortSyntax($tokens, $index);
                } else {
                    $this->fixToLongSyntax($tokens, $index);
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` `list` syntax.'))->setAllowedValues(['long', 'short'])->setDefault('long')->getOption()]);
    }
    /**
     * @param int $index
     */
    private function fixToLongSyntax(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        static $typesOfInterest = [[\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE], '['];
        $closeIndex = $tokens->getNextTokenOfKind($index, $typesOfInterest);
        if (!$tokens[$closeIndex]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE)) {
            return;
        }
        $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('(');
        $tokens[$closeIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(')');
        $tokens->insertAt($index, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_LIST, 'list']));
    }
    /**
     * @param int $index
     */
    private function fixToShortSyntax(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
        $closeIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
        $tokens[$openIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, '[']);
        $tokens[$closeIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, ']']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
