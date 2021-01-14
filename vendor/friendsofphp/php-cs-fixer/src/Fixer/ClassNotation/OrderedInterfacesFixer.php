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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ClassNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 */
final class OrderedInterfacesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /** @internal */
    const OPTION_DIRECTION = 'direction';
    /** @internal */
    const OPTION_ORDER = 'order';
    /** @internal */
    const DIRECTION_ASCEND = 'ascend';
    /** @internal */
    const DIRECTION_DESCEND = 'descend';
    /** @internal */
    const ORDER_ALPHA = 'alpha';
    /** @internal */
    const ORDER_LENGTH = 'length';
    /**
     * Array of supported directions in configuration.
     *
     * @var string[]
     */
    private $supportedDirectionOptions = [self::DIRECTION_ASCEND, self::DIRECTION_DESCEND];
    /**
     * Array of supported orders in configuration.
     *
     * @var string[]
     */
    private $supportedOrderOptions = [self::ORDER_ALPHA, self::ORDER_LENGTH];
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Orders the interfaces in an `implements` or `interface extends` clause.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nfinal class ExampleA implements Gamma, Alpha, Beta {}\n\ninterface ExampleB extends Gamma, Alpha, Beta {}\n"), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nfinal class ExampleA implements Gamma, Alpha, Beta {}\n\ninterface ExampleB extends Gamma, Alpha, Beta {}\n", [self::OPTION_DIRECTION => self::DIRECTION_DESCEND]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nfinal class ExampleA implements MuchLonger, Short, Longer {}\n\ninterface ExampleB extends MuchLonger, Short, Longer {}\n", [self::OPTION_ORDER => self::ORDER_LENGTH]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nfinal class ExampleA implements MuchLonger, Short, Longer {}\n\ninterface ExampleB extends MuchLonger, Short, Longer {}\n", [self::OPTION_ORDER => self::ORDER_LENGTH, self::OPTION_DIRECTION => self::DIRECTION_DESCEND])], null, "Risky for `implements` when specifying both an interface and its parent interface, because PHP doesn't break on `parent, child` but does on `child, parent`.");
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_IMPLEMENTS) || $tokens->isAllTokenKindsFound([\T_INTERFACE, \T_EXTENDS]);
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
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_IMPLEMENTS)) {
                if (!$token->isGivenKind(\T_EXTENDS)) {
                    continue;
                }
                $nameTokenIndex = $tokens->getPrevMeaningfulToken($index);
                $interfaceTokenIndex = $tokens->getPrevMeaningfulToken($nameTokenIndex);
                $interfaceToken = $tokens[$interfaceTokenIndex];
                if (!$interfaceToken->isGivenKind(\T_INTERFACE)) {
                    continue;
                }
            }
            $interfaceIndex = 0;
            $interfaces = [['tokens' => []]];
            $implementsStart = $index + 1;
            $classStart = $tokens->getNextTokenOfKind($implementsStart, ['{']);
            $implementsEnd = $tokens->getPrevNonWhitespace($classStart);
            for ($i = $implementsStart; $i <= $implementsEnd; ++$i) {
                if ($tokens[$i]->equals(',')) {
                    ++$interfaceIndex;
                    $interfaces[$interfaceIndex] = ['tokens' => []];
                    continue;
                }
                $interfaces[$interfaceIndex]['tokens'][] = $tokens[$i];
            }
            if (1 === \count($interfaces)) {
                continue;
            }
            foreach ($interfaces as $interfaceIndex => $interface) {
                $interfaceTokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromArray($interface['tokens'], \false);
                $normalized = '';
                $actualInterfaceIndex = $interfaceTokens->getNextMeaningfulToken(-1);
                while ($interfaceTokens->offsetExists($actualInterfaceIndex)) {
                    $token = $interfaceTokens[$actualInterfaceIndex];
                    if (null === $token || $token->isComment() || $token->isWhitespace()) {
                        break;
                    }
                    $normalized .= \str_replace('\\', ' ', $token->getContent());
                    ++$actualInterfaceIndex;
                }
                $interfaces[$interfaceIndex]['normalized'] = $normalized;
                $interfaces[$interfaceIndex]['originalIndex'] = $interfaceIndex;
            }
            \usort($interfaces, function (array $first, array $second) {
                $score = self::ORDER_LENGTH === $this->configuration[self::OPTION_ORDER] ? \strlen($first['normalized']) - \strlen($second['normalized']) : \strcasecmp($first['normalized'], $second['normalized']);
                if (self::DIRECTION_DESCEND === $this->configuration[self::OPTION_DIRECTION]) {
                    $score *= -1;
                }
                return $score;
            });
            $changed = \false;
            foreach ($interfaces as $interfaceIndex => $interface) {
                if ($interface['originalIndex'] !== $interfaceIndex) {
                    $changed = \true;
                    break;
                }
            }
            if (!$changed) {
                continue;
            }
            $newTokens = \array_shift($interfaces)['tokens'];
            foreach ($interfaces as $interface) {
                \array_push($newTokens, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token(','), ...$interface['tokens']);
            }
            $tokens->overrideRange($implementsStart, $implementsEnd, $newTokens);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder(self::OPTION_ORDER, 'How the interfaces should be ordered'))->setAllowedValues($this->supportedOrderOptions)->setDefault(self::ORDER_ALPHA)->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder(self::OPTION_DIRECTION, 'Which direction the interfaces should be ordered'))->setAllowedValues($this->supportedDirectionOptions)->setDefault(self::DIRECTION_ASCEND)->getOption()]);
    }
}
