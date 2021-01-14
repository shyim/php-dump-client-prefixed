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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\FunctionNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options;
/**
 * Fixer for rules defined in PSR2 ¶4.4, ¶4.6.
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
final class MethodArgumentSpaceFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param int $index
     */
    public function fixSpace(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        @\trigger_error(__METHOD__ . ' is deprecated and will be removed in 3.0.', \E_USER_DEPRECATED);
        $this->fixSpace2($tokens, $index);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);\n", null), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);\n", ['keep_multiple_spaces_after_comma' => \false]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);\n", ['keep_multiple_spaces_after_comma' => \true]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,\n    2);\n", ['on_multiline' => 'ensure_fully_multiline']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\n    \$a=10,\n    \$b=20,\n    \$c=30\n) {}\nsample(\n    1,\n    2\n);\n", ['on_multiline' => 'ensure_single_line']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,  \n    2);\nsample('foo',    'foobarbaz', 'baz');\nsample('foobar', 'bar',       'baz');\n", ['on_multiline' => 'ensure_fully_multiline', 'keep_multiple_spaces_after_comma' => \true]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,  \n    2);\nsample('foo',    'foobarbaz', 'baz');\nsample('foobar', 'bar',       'baz');\n", ['on_multiline' => 'ensure_fully_multiline', 'keep_multiple_spaces_after_comma' => \false]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'SAMPLE'
<?php

namespace _PhpScoper3fe455fa007d;

\_PhpScoper3fe455fa007d\sample(<<<EOD
foo
EOD
, 'bar');

SAMPLE
, new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70300), ['after_heredoc' => \true])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if ($this->configuration['ensure_fully_multiline'] && 'ignore' === $this->configuration['on_multiline']) {
            $this->configuration['on_multiline'] = 'ensure_fully_multiline';
        }
    }
    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer.
     * Must run after BracesFixer, CombineNestedDirnameFixer, ImplodeCallFixer, MethodChainingIndentationFixer, NoUselessSprintfFixer, PowToExponentiationFixer.
     */
    public function getPriority()
    {
        return -30;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $expectedTokens = [\T_LIST, \T_FUNCTION, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA];
        if (\PHP_VERSION_ID >= 70400) {
            $expectedTokens[] = \T_FN;
        }
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->equals('(')) {
                continue;
            }
            $meaningfulTokenBeforeParenthesis = $tokens[$tokens->getPrevMeaningfulToken($index)];
            if ($meaningfulTokenBeforeParenthesis->isKeyword() && !$meaningfulTokenBeforeParenthesis->isGivenKind($expectedTokens)) {
                continue;
            }
            $isMultiline = $this->fixFunction($tokens, $index);
            if ($isMultiline && 'ensure_fully_multiline' === $this->configuration['on_multiline'] && !$meaningfulTokenBeforeParenthesis->isGivenKind(\T_LIST)) {
                $this->ensureFunctionFullyMultiline($tokens, $index);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('keep_multiple_spaces_after_comma', 'Whether keep multiple spaces after comma.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('ensure_fully_multiline', 'ensure every argument of a multiline argument list is on its own line'))->setAllowedTypes(['bool'])->setDefault(\false)->setDeprecationMessage('Use option `on_multiline` instead.')->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('on_multiline', 'Defines how to handle function arguments lists that contain newlines.'))->setAllowedValues(['ignore', 'ensure_single_line', 'ensure_fully_multiline'])->setDefault('ignore')->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('after_heredoc', 'Whether the whitespace between heredoc end and comma should be removed.'))->setAllowedTypes(['bool'])->setDefault(\false)->setNormalizer(static function (\_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options $options, $value) {
            if (\PHP_VERSION_ID < 70300 && $value) {
                throw new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException('"after_heredoc" option can only be enabled with PHP 7.3+.');
            }
            return $value;
        })->getOption()]);
    }
    /**
     * Fix arguments spacing for given function.
     *
     * @param Tokens $tokens             Tokens to handle
     * @param int    $startFunctionIndex Start parenthesis position
     *
     * @return bool whether the function is multiline
     */
    private function fixFunction(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startFunctionIndex)
    {
        $endFunctionIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);
        $isMultiline = \false;
        $firstWhitespaceIndex = $this->findWhitespaceIndexAfterParenthesis($tokens, $startFunctionIndex, $endFunctionIndex);
        $lastWhitespaceIndex = $this->findWhitespaceIndexAfterParenthesis($tokens, $endFunctionIndex, $startFunctionIndex);
        foreach ([$firstWhitespaceIndex, $lastWhitespaceIndex] as $index) {
            if (null === $index || !\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $tokens[$index]->getContent())) {
                continue;
            }
            if ('ensure_single_line' !== $this->configuration['on_multiline']) {
                $isMultiline = \true;
                continue;
            }
            $newLinesRemoved = $this->ensureSingleLine($tokens, $index);
            if (!$newLinesRemoved) {
                $isMultiline = \true;
            }
        }
        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];
            if ($token->equals(')')) {
                $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }
            if ($token->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                continue;
            }
            if ($token->equals('}')) {
                $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }
            if ($token->equals(',')) {
                $this->fixSpace2($tokens, $index);
                if (!$isMultiline && $this->isNewline($tokens[$index + 1])) {
                    $isMultiline = \true;
                    break;
                }
            }
        }
        return $isMultiline;
    }
    /**
     * @param int $startParenthesisIndex
     * @param int $endParenthesisIndex
     *
     * @return null|int
     */
    private function findWhitespaceIndexAfterParenthesis(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startParenthesisIndex, $endParenthesisIndex)
    {
        $direction = $endParenthesisIndex > $startParenthesisIndex ? 1 : -1;
        $startIndex = $startParenthesisIndex + $direction;
        $endIndex = $endParenthesisIndex - $direction;
        for ($index = $startIndex; $index !== $endIndex; $index += $direction) {
            $token = $tokens[$index];
            if ($token->isWhitespace()) {
                return $index;
            }
            if (!$token->isComment()) {
                break;
            }
        }
        return null;
    }
    /**
     * @param int $index
     *
     * @return bool Whether newlines were removed from the whitespace token
     */
    private function ensureSingleLine(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $previousToken = $tokens[$index - 1];
        if ($previousToken->isComment() && 0 !== \strpos($previousToken->getContent(), '/*')) {
            return \false;
        }
        $content = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\R\\h*/', '', $tokens[$index]->getContent());
        if ('' !== $content) {
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]);
        } else {
            $tokens->clearAt($index);
        }
        return \true;
    }
    /**
     * @param int $startFunctionIndex
     */
    private function ensureFunctionFullyMultiline(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startFunctionIndex)
    {
        // find out what the indentation is
        $searchIndex = $startFunctionIndex;
        do {
            $prevWhitespaceTokenIndex = $tokens->getPrevTokenOfKind($searchIndex, [[\T_WHITESPACE]]);
            $searchIndex = $prevWhitespaceTokenIndex;
        } while (null !== $prevWhitespaceTokenIndex && \false === \strpos($tokens[$prevWhitespaceTokenIndex]->getContent(), "\n"));
        if (null === $prevWhitespaceTokenIndex) {
            $existingIndentation = '';
        } else {
            $existingIndentation = $tokens[$prevWhitespaceTokenIndex]->getContent();
            $lastLineIndex = \strrpos($existingIndentation, "\n");
            $existingIndentation = \false === $lastLineIndex ? $existingIndentation : \substr($existingIndentation, $lastLineIndex + 1);
        }
        $indentation = $existingIndentation . $this->whitespacesConfig->getIndent();
        $endFunctionIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);
        $wasWhitespaceBeforeEndFunctionAddedAsNewToken = $tokens->ensureWhitespaceAtIndex($tokens[$endFunctionIndex - 1]->isWhitespace() ? $endFunctionIndex - 1 : $endFunctionIndex, 0, $this->whitespacesConfig->getLineEnding() . $existingIndentation);
        if ($wasWhitespaceBeforeEndFunctionAddedAsNewToken) {
            ++$endFunctionIndex;
        }
        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];
            // skip nested method calls and arrays
            if ($token->equals(')')) {
                $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }
            // skip nested arrays
            if ($token->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                continue;
            }
            if ($token->equals('}')) {
                $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }
            if ($token->equals(',') && !$tokens[$tokens->getNextMeaningfulToken($index)]->equals(')')) {
                $this->fixNewline($tokens, $index, $indentation);
            }
        }
        $this->fixNewline($tokens, $startFunctionIndex, $indentation, \false);
    }
    /**
     * Method to insert newline after comma or opening parenthesis.
     *
     * @param int    $index       index of a comma
     * @param string $indentation the indentation that should be used
     * @param bool   $override    whether to override the existing character or not
     */
    private function fixNewline(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $indentation, $override = \true)
    {
        if ($tokens[$index + 1]->isComment()) {
            return;
        }
        if ($tokens[$index + 2]->isComment()) {
            $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index + 2);
            if (!$this->isNewline($tokens[$nextMeaningfulTokenIndex - 1])) {
                $tokens->ensureWhitespaceAtIndex($nextMeaningfulTokenIndex, 0, $this->whitespacesConfig->getLineEnding() . $indentation);
            }
            return;
        }
        $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index);
        if ($tokens[$nextMeaningfulTokenIndex]->equals(')')) {
            return;
        }
        $tokens->ensureWhitespaceAtIndex($index + 1, 0, $this->whitespacesConfig->getLineEnding() . $indentation);
    }
    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param int $index
     */
    private function fixSpace2(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        // remove space before comma if exist
        if ($tokens[$index - 1]->isWhitespace()) {
            $prevIndex = $tokens->getPrevNonWhitespace($index - 1);
            if (!$tokens[$prevIndex]->equals(',') && !$tokens[$prevIndex]->isComment() && ($this->configuration['after_heredoc'] || !$tokens[$prevIndex]->isGivenKind(\T_END_HEREDOC))) {
                $tokens->clearAt($index - 1);
            }
        }
        $nextIndex = $index + 1;
        $nextToken = $tokens[$nextIndex];
        // Two cases for fix space after comma (exclude multiline comments)
        //  1) multiple spaces after comma
        //  2) no space after comma
        if ($nextToken->isWhitespace()) {
            $newContent = $nextToken->getContent();
            if ('ensure_single_line' === $this->configuration['on_multiline']) {
                $newContent = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\R/', '', $newContent);
            }
            if ((!$this->configuration['keep_multiple_spaces_after_comma'] || \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R/', $newContent)) && !$this->isCommentLastLineToken($tokens, $index + 2)) {
                $newContent = \ltrim($newContent, " \t");
            }
            $tokens[$nextIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, '' === $newContent ? ' ' : $newContent]);
            return;
        }
        if (!$this->isCommentLastLineToken($tokens, $index + 1)) {
            $tokens->insertAt($index + 1, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
        }
    }
    /**
     * Check if last item of current line is a comment.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     *
     * @return bool
     */
    private function isCommentLastLineToken(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isComment() || !$tokens[$index + 1]->isWhitespace()) {
            return \false;
        }
        $content = $tokens[$index + 1]->getContent();
        return $content !== \ltrim($content, "\r\n");
    }
    /**
     * Checks if token is new line.
     *
     * @return bool
     */
    private function isNewline(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token $token)
    {
        return $token->isWhitespace() && \false !== \strpos($token->getContent(), "\n");
    }
}
