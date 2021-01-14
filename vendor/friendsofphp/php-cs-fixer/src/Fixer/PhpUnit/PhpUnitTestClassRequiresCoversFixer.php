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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit;

use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock;
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Line;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitTestClassRequiresCoversFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Adds a default `@coversNothing` annotation to PHPUnit test classes that have no `@covers*` annotation.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertSame(a(), b());
    }
}
')]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[\T_CLASS]]);
        $prevIndex = $tokens->getPrevMeaningfulToken($classIndex);
        // don't add `@covers` annotation for abstract base classes
        if ($tokens[$prevIndex]->isGivenKind(\T_ABSTRACT)) {
            return;
        }
        $index = $tokens[$prevIndex]->isGivenKind(\T_FINAL) ? $prevIndex : $classIndex;
        $indent = $tokens[$index - 1]->isGivenKind(\T_WHITESPACE) ? \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/^.*\\R*/', '', $tokens[$index - 1]->getContent()) : '';
        $prevIndex = $tokens->getPrevNonWhitespace($index);
        if ($tokens[$prevIndex]->isGivenKind(\T_DOC_COMMENT)) {
            $docIndex = $prevIndex;
            $docContent = $tokens[$docIndex]->getContent();
            // ignore one-line phpdocs like `/** foo */`, as there is no place to put new annotations
            if (\false === \strpos($docContent, "\n")) {
                return;
            }
            $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($docContent);
            // skip if already has annotation
            if (!empty($doc->getAnnotationsOfType(['covers', 'coversDefaultClass', 'coversNothing']))) {
                return;
            }
        } else {
            $docIndex = $index;
            $tokens->insertAt($docIndex, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \sprintf('/**%s%s */', $this->whitespacesConfig->getLineEnding(), $indent)]), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \sprintf('%s%s', $this->whitespacesConfig->getLineEnding(), $indent)])]);
            if (!$tokens[$docIndex - 1]->isGivenKind(\T_WHITESPACE)) {
                $extraNewLines = $this->whitespacesConfig->getLineEnding();
                if (!$tokens[$docIndex - 1]->isGivenKind(\T_OPEN_TAG)) {
                    $extraNewLines .= $this->whitespacesConfig->getLineEnding();
                }
                $tokens->insertAt($docIndex, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $extraNewLines . $indent])]);
                ++$docIndex;
            }
            $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($tokens[$docIndex]->getContent());
        }
        $lines = $doc->getLines();
        \array_splice($lines, \count($lines) - 1, 0, [new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Line(\sprintf('%s * @coversNothing%s', $indent, $this->whitespacesConfig->getLineEnding()))]);
        $tokens[$docIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \implode('', $lines)]);
    }
}
