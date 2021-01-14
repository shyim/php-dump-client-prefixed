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

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFunctionReferenceFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class RandomApiMigrationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFunctionReferenceFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @var array
     */
    private static $argumentCounts = ['getrandmax' => [0], 'mt_rand' => [1, 2], 'rand' => [0, 2], 'srand' => [0, 1]];
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        foreach ($this->configuration['replacements'] as $functionName => $replacement) {
            $this->configuration['replacements'][$functionName] = ['alternativeName' => $replacement, 'argumentCount' => self::$argumentCounts[$functionName]];
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Replaces `rand`, `srand`, `getrandmax` functions calls with their `mt_*` analogs.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = getrandmax();\n\$a = rand(\$b, \$c);\n\$a = srand();\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = getrandmax();\n\$a = rand(\$b, \$c);\n\$a = srand();\n", ['replacements' => ['getrandmax' => 'mt_getrandmax']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php \$a = rand(\$b, \$c);\n", ['replacements' => ['rand' => 'random_int']])], null, 'Risky when the configured functions are overridden.');
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
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $argumentsAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        foreach ($this->configuration['replacements'] as $functionIdentity => $functionReplacement) {
            if ($functionIdentity === $functionReplacement['alternativeName']) {
                continue;
            }
            $currIndex = 0;
            while (null !== $currIndex) {
                // try getting function reference and translate boundaries for humans
                $boundaries = $this->find($functionIdentity, $tokens, $currIndex, $tokens->count() - 1);
                if (null === $boundaries) {
                    // next function search, as current one not found
                    continue 2;
                }
                list($functionName, $openParenthesis, $closeParenthesis) = $boundaries;
                $count = $argumentsAnalyzer->countArguments($tokens, $openParenthesis, $closeParenthesis);
                if (!\in_array($count, $functionReplacement['argumentCount'], \true)) {
                    continue 2;
                }
                // analysing cursor shift, so nested calls could be processed
                $currIndex = $openParenthesis;
                $tokens[$functionName] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, $functionReplacement['alternativeName']]);
                if (0 === $count && 'random_int' === $functionReplacement['alternativeName']) {
                    $tokens->insertAt($currIndex + 1, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_LNUMBER, '0']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(','), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, 'getrandmax']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token('('), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(')')]);
                    $currIndex += 6;
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless('replacements', [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('replacements', 'Mapping between replaced functions with the new ones.'))->setAllowedTypes(['array'])->setAllowedValues([static function ($value) {
            foreach ($value as $functionName => $replacement) {
                if (!\array_key_exists($functionName, self::$argumentCounts)) {
                    throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Function "%s" is not handled by the fixer.', $functionName));
                }
                if (!\is_string($replacement)) {
                    throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Replacement for function "%s" must be a string, "%s" given.', $functionName, \is_object($replacement) ? \get_class($replacement) : \gettype($replacement)));
                }
            }
            return \true;
        }])->setDefault(['getrandmax' => 'mt_getrandmax', 'rand' => 'mt_rand', 'srand' => 'mt_srand'])->getOption()], $this->getName());
    }
}
