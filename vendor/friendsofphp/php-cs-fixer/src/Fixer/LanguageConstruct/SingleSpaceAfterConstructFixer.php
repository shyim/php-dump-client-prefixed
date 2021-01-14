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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\LanguageConstruct;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Andreas Möller <am@localheinz.com>
 */
final class SingleSpaceAfterConstructFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @var array<string, null|int>
     */
    private static $tokenMap = ['abstract' => \T_ABSTRACT, 'as' => \T_AS, 'attribute' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ATTRIBUTE_CLOSE, 'break' => \T_BREAK, 'case' => \T_CASE, 'catch' => \T_CATCH, 'class' => \T_CLASS, 'clone' => \T_CLONE, 'const' => \T_CONST, 'const_import' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, 'continue' => \T_CONTINUE, 'do' => \T_DO, 'echo' => \T_ECHO, 'else' => \T_ELSE, 'elseif' => \T_ELSEIF, 'extends' => \T_EXTENDS, 'final' => \T_FINAL, 'finally' => \T_FINALLY, 'for' => \T_FOR, 'foreach' => \T_FOREACH, 'function' => \T_FUNCTION, 'function_import' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT, 'global' => \T_GLOBAL, 'goto' => \T_GOTO, 'if' => \T_IF, 'implements' => \T_IMPLEMENTS, 'include' => \T_INCLUDE, 'include_once' => \T_INCLUDE_ONCE, 'instanceof' => \T_INSTANCEOF, 'insteadof' => \T_INSTEADOF, 'interface' => \T_INTERFACE, 'match' => null, 'named_argument' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NAMED_ARGUMENT_COLON, 'new' => \T_NEW, 'open_tag_with_echo' => \T_OPEN_TAG_WITH_ECHO, 'php_open' => \T_OPEN_TAG, 'print' => \T_PRINT, 'private' => \T_PRIVATE, 'protected' => \T_PROTECTED, 'public' => \T_PUBLIC, 'require' => \T_REQUIRE, 'require_once' => \T_REQUIRE_ONCE, 'return' => \T_RETURN, 'static' => \T_STATIC, 'throw' => \T_THROW, 'trait' => \T_TRAIT, 'try' => \T_TRY, 'use' => \T_USE, 'use_lambda' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA, 'use_trait' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT, 'var' => \T_VAR, 'while' => \T_WHILE, 'yield' => \T_YIELD, 'yield_from' => null];
    /**
     * @var array<string, int>
     */
    private $fixTokenMap = [];
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if (\defined('T_YIELD_FROM')) {
            self::$tokenMap['yield_from'] = \T_YIELD_FROM;
        }
        if (\defined('T_MATCH')) {
            self::$tokenMap['match'] = \T_MATCH;
        }
        $this->fixTokenMap = [];
        foreach ($this->configuration['constructs'] as $key) {
            $this->fixTokenMap[$key] = self::$tokenMap[$key];
        }
        if (isset($this->fixTokenMap['public'])) {
            $this->fixTokenMap['constructor_public'] = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC;
        }
        if (isset($this->fixTokenMap['protected'])) {
            $this->fixTokenMap['constructor_protected'] = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED;
        }
        if (isset($this->fixTokenMap['private'])) {
            $this->fixTokenMap['constructor_private'] = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Ensures a single space after language constructs.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

throw  new  \\Exception();
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

echo  "Hello!";
', ['constructs' => ['echo']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

yield  from  baz();
', new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70000), ['constructs' => ['yield_from']])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(\array_values($this->fixTokenMap)) && !$tokens->hasAlternativeSyntax();
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokenKinds = \array_values($this->fixTokenMap);
        for ($index = $tokens->count() - 2; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind($tokenKinds)) {
                continue;
            }
            $whitespaceTokenIndex = $index + 1;
            if (';' === $tokens[$whitespaceTokenIndex]->getContent()) {
                continue;
            }
            if ($token->isGivenKind(\T_STATIC) && !$tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind([\T_FUNCTION, \T_VARIABLE])) {
                continue;
            }
            if ($token->isGivenKind(\T_OPEN_TAG)) {
                if ($tokens[$whitespaceTokenIndex]->equals([\T_WHITESPACE]) && \false === \strpos($token->getContent(), "\n")) {
                    $tokens->clearAt($whitespaceTokenIndex);
                }
                continue;
            }
            if ($token->isGivenKind(\T_CLASS) && $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(')) {
                continue;
            }
            if ($token->isGivenKind([\T_EXTENDS, \T_IMPLEMENTS]) && $this->isMultilineExtendsOrImplementsWithMoreThanOneAncestor($tokens, $index)) {
                continue;
            }
            if ($token->isGivenKind(\T_RETURN) && $this->isMultiLineReturn($tokens, $index)) {
                continue;
            }
            if (!$tokens[$whitespaceTokenIndex]->equals([\T_WHITESPACE])) {
                $tokens->insertAt($whitespaceTokenIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
            } elseif (' ' !== $tokens[$whitespaceTokenIndex]->getContent()) {
                $tokens[$whitespaceTokenIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
            }
            if (70000 <= \PHP_VERSION_ID && $token->isGivenKind(\T_YIELD_FROM) && 'yield from' !== \strtolower($token->getContent())) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_YIELD_FROM, \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\s+/', ' ', $token->getContent())]);
            }
        }
    }
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('constructs', 'List of constructs which must be followed by a single space.'))->setAllowedTypes(['array'])->setAllowedValues([new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset(\array_keys(self::$tokenMap))])->setDefault(\array_keys(self::$tokenMap))->getOption()]);
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isMultiLineReturn(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        ++$index;
        $tokenFollowingReturn = $tokens[$index];
        if (!$tokenFollowingReturn->isGivenKind(\T_WHITESPACE) || \false === \strpos($tokenFollowingReturn->getContent(), "\n")) {
            return \false;
        }
        $nestedCount = 0;
        for ($indexEnd = \count($tokens) - 1, ++$index; $index < $indexEnd; ++$index) {
            if (\false !== \strpos($tokens[$index]->getContent(), "\n")) {
                return \true;
            }
            if ($tokens[$index]->equals('{')) {
                ++$nestedCount;
            } elseif ($tokens[$index]->equals('}')) {
                --$nestedCount;
            } elseif (0 === $nestedCount && $tokens[$index]->equalsAny([';', [\T_CLOSE_TAG]])) {
                break;
            }
        }
        return \false;
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isMultilineExtendsOrImplementsWithMoreThanOneAncestor(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $hasMoreThanOneAncestor = \false;
        while (++$index) {
            $token = $tokens[$index];
            if ($token->equals(',')) {
                $hasMoreThanOneAncestor = \true;
                continue;
            }
            if ($token->equals('{')) {
                return \false;
            }
            if ($hasMoreThanOneAncestor && \false !== \strpos($token->getContent(), "\n")) {
                return \true;
            }
        }
        return \false;
    }
}
