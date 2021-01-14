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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Phpdoc;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author Julien Falque <julien.falque@gmail.com>
 */
final class AlignMultilineCommentFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    private $tokenKinds;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->tokenKinds = [\T_DOC_COMMENT];
        if ('phpdocs_only' !== $this->configuration['comment_type']) {
            $this->tokenKinds[] = \T_COMMENT;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
    /**
            * This is a DOC Comment
with a line not prefixed with asterisk

   */
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
    /*
            * This is a doc-like multiline comment
*/
', ['comment_type' => 'phpdocs_like']), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
    /*
            * This is a doc-like multiline comment
with a line not prefixed with asterisk

   */
', ['comment_type' => 'all_multiline'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocTrimConsecutiveBlankLineSeparationFixer.
     * Must run after ArrayIndentationFixer.
     */
    public function getPriority()
    {
        return -40;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound($this->tokenKinds);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind($this->tokenKinds)) {
                continue;
            }
            $whitespace = '';
            $previousIndex = $index - 1;
            if ($tokens[$previousIndex]->isWhitespace()) {
                $whitespace = $tokens[$previousIndex]->getContent();
                --$previousIndex;
            }
            if ($tokens[$previousIndex]->isGivenKind(\T_OPEN_TAG)) {
                $whitespace = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\S/', '', $tokens[$previousIndex]->getContent()) . $whitespace;
            }
            if (1 !== \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R(\\h*)$/', $whitespace, $matches)) {
                continue;
            }
            if ($token->isGivenKind(\T_COMMENT) && 'all_multiline' !== $this->configuration['comment_type'] && 1 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/\\R(?:\\R|\\s*[^\\s\\*])/', $token->getContent())) {
                continue;
            }
            $indentation = $matches[1];
            $lines = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::split('/\\R/u', $token->getContent());
            foreach ($lines as $lineNumber => $line) {
                if (0 === $lineNumber) {
                    continue;
                }
                $line = \ltrim($line);
                if ($token->isGivenKind(\T_COMMENT) && (!isset($line[0]) || '*' !== $line[0])) {
                    continue;
                }
                if (!isset($line[0])) {
                    $line = '*';
                } elseif ('*' !== $line[0]) {
                    $line = '* ' . $line;
                }
                $lines[$lineNumber] = $indentation . ' ' . $line;
            }
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), \implode($lineEnding, $lines)]);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('comment_type', 'Whether to fix PHPDoc comments only (`phpdocs_only`), any multi-line comment whose lines all start with an asterisk (`phpdocs_like`) or any multi-line comment (`all_multiline`).'))->setAllowedValues(['phpdocs_only', 'phpdocs_like', 'all_multiline'])->setDefault('phpdocs_only')->getOption()]);
    }
}
