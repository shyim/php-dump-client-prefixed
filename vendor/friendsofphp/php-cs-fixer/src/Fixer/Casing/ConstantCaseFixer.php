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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Casing;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * Fixer for constants case.
 *
 * @author Pol Dellaiera <pol.dellaiera@protonmail.com>
 */
final class ConstantCaseFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * Hold the function that will be used to convert the constants.
     *
     * @var callable
     */
    private $fixFunction;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if ('lower' === $this->configuration['case']) {
            $this->fixFunction = static function ($token) {
                return \strtolower($token);
            };
        }
        if ('upper' === $this->configuration['case']) {
            $this->fixFunction = static function ($token) {
                return \strtoupper($token);
            };
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n", ['case' => 'upper'])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('case', 'Whether to use the `upper` or `lower` case syntax.'))->setAllowedValues(['upper', 'lower'])->setDefault('lower')->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $fixFunction = $this->fixFunction;
        foreach ($tokens as $index => $token) {
            if (!$token->isNativeConstant()) {
                continue;
            }
            if ($this->isNeighbourAccepted($tokens, $tokens->getPrevMeaningfulToken($index)) && $this->isNeighbourAccepted($tokens, $tokens->getNextMeaningfulToken($index))) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), $fixFunction($token->getContent())]);
            }
        }
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isNeighbourAccepted(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        static $forbiddenTokens = [\T_AS, \T_CLASS, \T_CONST, \T_EXTENDS, \T_IMPLEMENTS, \T_INSTANCEOF, \T_INSTEADOF, \T_INTERFACE, \T_NEW, \T_NS_SEPARATOR, \T_OBJECT_OPERATOR, \T_PAAMAYIM_NEKUDOTAYIM, \T_TRAIT, \T_USE, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA];
        $token = $tokens[$index];
        if ($token->equalsAny(['{', '}'])) {
            return \false;
        }
        return !$token->isGivenKind($forbiddenTokens);
    }
}
