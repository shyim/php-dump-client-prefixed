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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Whitespace;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class NoExtraBlankLinesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var string[]
     */
    private static $availableTokens = ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'useTrait', 'use_trait'];
    /**
     * @var array<int, string> key is token id, value is name of callback
     */
    private $tokenKindCallbackMap;
    /**
     * @var array<string, string> token prototype, value is name of callback
     */
    private $tokenEqualsMap;
    /**
     * @var Tokens
     */
    private $tokens;
    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        static $reprToTokenMap = ['break' => \T_BREAK, 'case' => \T_CASE, 'continue' => \T_CONTINUE, 'curly_brace_block' => '{', 'default' => \T_DEFAULT, 'extra' => \T_WHITESPACE, 'parenthesis_brace_block' => '(', 'return' => \T_RETURN, 'square_brace_block' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN, 'switch' => \T_SWITCH, 'throw' => \T_THROW, 'use' => \T_USE, 'use_trait' => \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT];
        static $tokenKindCallbackMap = [\T_BREAK => 'fixAfterToken', \T_CASE => 'fixAfterToken', \T_CONTINUE => 'fixAfterToken', \T_DEFAULT => 'fixAfterToken', \T_RETURN => 'fixAfterToken', \T_SWITCH => 'fixAfterToken', \T_THROW => 'fixAfterToken', \T_USE => 'removeBetweenUse', \T_WHITESPACE => 'removeMultipleBlankLines', \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT => 'removeBetweenUse', \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN => 'fixStructureOpenCloseIfMultiLine'];
        static $tokenEqualsMap = [
            '{' => 'fixStructureOpenCloseIfMultiLine',
            // i.e. not: CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN
            '(' => 'fixStructureOpenCloseIfMultiLine',
        ];
        $tokensAssoc = \array_flip(\array_intersect_key($reprToTokenMap, \array_flip($this->configuration['tokens'])));
        $this->tokenKindCallbackMap = \array_intersect_key($tokenKindCallbackMap, $tokensAssoc);
        $this->tokenEqualsMap = \array_intersect_key($tokenEqualsMap, $tokensAssoc);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Removes extra blank lines and/or blank lines following configuration.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = array("foo");


$bar = "bar";
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

switch ($foo) {
    case 41:
        echo "foo";
        break;

    case 42:
        break;
}
', ['tokens' => ['break']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

for ($i = 0; $i < 9000; ++$i) {
    if (true) {
        continue;

    }
}
', ['tokens' => ['continue']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

for ($i = 0; $i < 9000; ++$i) {

    echo $i;

}
', ['tokens' => ['curly_brace_block']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = array("foo");


$bar = "bar";
', ['tokens' => ['extra']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = array(

    "foo"

);
', ['tokens' => ['parenthesis_brace_block']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

function foo($bar)
{
    return $bar;

}
', ['tokens' => ['return']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = [

    "foo"

];
', ['tokens' => ['square_brace_block']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

function foo($bar)
{
    throw new \\Exception("Hello!");

}
', ['tokens' => ['throw']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

namespace Foo;

use Bar\\Baz;

use Baz\\Bar;

class Bar
{
}
', ['tokens' => ['use']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

class Foo
{
    use Bar;

    use Baz;
}
', ['tokens' => ['use_trait']]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
switch($a) {

    case 1:

    default:

        echo 3;
}
', ['tokens' => ['switch', 'case', 'default']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BlankLineBeforeStatementFixer.
     * Must run after CombineConsecutiveUnsetsFixer, FunctionToConstantFixer, NoEmptyCommentFixer, NoEmptyPhpdocFixer, NoEmptyStatementFixer, NoUnusedImportsFixer, NoUselessElseFixer, NoUselessReturnFixer, NoUselessSprintfFixer.
     */
    public function getPriority()
    {
        return -20;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $this->tokens = $tokens;
        $this->tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($this->tokens);
        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $this->fixByToken($tokens[$index], $index);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $that = $this;
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless('tokens', [(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('tokens', 'List of tokens to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset(self::$availableTokens)])->setNormalizer(static function (\_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options $options, $tokens) use($that) {
            foreach ($tokens as &$token) {
                if ('useTrait' === $token) {
                    $message = "Token \"useTrait\" in option \"tokens\" for rule \"{$that->getName()}\" is deprecated and will be removed in 3.0, use \"use_trait\" instead.";
                    if (\getenv('PHP_CS_FIXER_FUTURE_MODE')) {
                        throw new \_PhpScoper3fe455fa007d\PhpCsFixer\ConfigurationException\InvalidConfigurationException("{$message} This check was performed as `PHP_CS_FIXER_FUTURE_MODE` env var is set.");
                    }
                    @\trigger_error($message, \E_USER_DEPRECATED);
                    $token = 'use_trait';
                    break;
                }
            }
            return $tokens;
        })->setDefault(['extra'])->getOption()], $this->getName());
    }
    private function fixByToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token, $index)
    {
        foreach ($this->tokenKindCallbackMap as $kind => $callback) {
            if (!$token->isGivenKind($kind)) {
                continue;
            }
            $this->{$callback}($index);
            return;
        }
        foreach ($this->tokenEqualsMap as $equals => $callback) {
            if (!$token->equals($equals)) {
                continue;
            }
            $this->{$callback}($index);
            return;
        }
    }
    private function removeBetweenUse($index)
    {
        $next = $this->tokens->getNextTokenOfKind($index, [';', [\T_CLOSE_TAG]]);
        if (null === $next || $this->tokens[$next]->isGivenKind(\T_CLOSE_TAG)) {
            return;
        }
        $nextUseCandidate = $this->tokens->getNextMeaningfulToken($next);
        if (null === $nextUseCandidate || !$this->tokens[$nextUseCandidate]->isGivenKind($this->tokens[$index]->getId()) || !$this->containsLinebreak($index, $nextUseCandidate)) {
            return;
        }
        return $this->removeEmptyLinesAfterLineWithTokenAt($next);
    }
    private function removeMultipleBlankLines($index)
    {
        $expected = $this->tokens[$index - 1]->isGivenKind(\T_OPEN_TAG) && 1 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R$/', $this->tokens[$index - 1]->getContent()) ? 1 : 2;
        $parts = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::split('/(.*\\R)/', $this->tokens[$index]->getContent(), -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        $count = \count($parts);
        if ($count > $expected) {
            $this->tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \implode('', \array_slice($parts, 0, $expected)) . \rtrim($parts[$count - 1], "\r\n")]);
        }
    }
    private function fixAfterToken($index)
    {
        for ($i = $index - 1; $i > 0; --$i) {
            if ($this->tokens[$i]->isGivenKind(\T_FUNCTION) && $this->tokensAnalyzer->isLambda($i)) {
                return;
            }
            if ($this->tokens[$i]->isGivenKind(\T_CLASS) && $this->tokensAnalyzer->isAnonymousClass($i)) {
                return;
            }
            if ($this->tokens[$i]->isWhitespace() && \false !== \strpos($this->tokens[$i]->getContent(), "\n")) {
                break;
            }
        }
        $this->removeEmptyLinesAfterLineWithTokenAt($index);
    }
    /**
     * Remove white line(s) after the index of a block type,
     * but only if the block is not on one line.
     *
     * @param int $index body start
     */
    private function fixStructureOpenCloseIfMultiLine($index)
    {
        $blockTypeInfo = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::detectBlockType($this->tokens[$index]);
        $bodyEnd = $this->tokens->findBlockEnd($blockTypeInfo['type'], $index);
        for ($i = $bodyEnd - 1; $i >= $index; --$i) {
            if (\false !== \strpos($this->tokens[$i]->getContent(), "\n")) {
                $this->removeEmptyLinesAfterLineWithTokenAt($i);
                $this->removeEmptyLinesAfterLineWithTokenAt($index);
                break;
            }
        }
    }
    private function removeEmptyLinesAfterLineWithTokenAt($index)
    {
        // find the line break
        $tokenCount = \count($this->tokens);
        for ($end = $index; $end < $tokenCount; ++$end) {
            if ($this->tokens[$end]->equals('}') || \false !== \strpos($this->tokens[$end]->getContent(), "\n")) {
                break;
            }
        }
        if ($end === $tokenCount) {
            return;
            // not found, early return
        }
        $ending = $this->whitespacesConfig->getLineEnding();
        for ($i = $end; $i < $tokenCount && $this->tokens[$i]->isWhitespace(); ++$i) {
            $content = $this->tokens[$i]->getContent();
            if (\substr_count($content, "\n") < 1) {
                continue;
            }
            $pos = \strrpos($content, "\n");
            if ($pos + 2 <= \strlen($content)) {
                // preserve indenting where possible
                $newContent = $ending . \substr($content, $pos + 1);
            } else {
                $newContent = $ending;
            }
            $this->tokens[$i] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $newContent]);
        }
    }
    /**
     * @param int $startIndex
     * @param int $endIndex
     *
     * @return bool
     */
    private function containsLinebreak($startIndex, $endIndex)
    {
        for ($i = $endIndex; $i > $startIndex; --$i) {
            if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $this->tokens[$i]->getContent())) {
                return \true;
            }
        }
        return \false;
    }
}
