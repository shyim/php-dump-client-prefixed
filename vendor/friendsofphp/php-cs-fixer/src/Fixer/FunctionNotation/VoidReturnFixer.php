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
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation;
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Mark Nielsen
 */
final class VoidReturnFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Add `void` return type to functions with missing or empty return statements, but priority is given to `@return` annotations. Requires PHP >= 7.1.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(\$a) {};\n", new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70100))], null, 'Modifies the signature of functions.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocNoEmptyReturnFixer, ReturnTypeDeclarationFixer.
     * Must run after NoSuperfluousPhpdocTagsFixer, SimplifiedNullReturnFixer.
     */
    public function getPriority()
    {
        return 5;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70100 && $tokens->isTokenKindFound(\T_FUNCTION);
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
        // These cause syntax errors.
        static $excludeFuncNames = [[\T_STRING, '__construct'], [\T_STRING, '__destruct'], [\T_STRING, '__clone']];
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            $funcName = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$funcName]->equalsAny($excludeFuncNames, \false)) {
                continue;
            }
            $startIndex = $tokens->getNextTokenOfKind($index, ['{', ';']);
            if ($this->hasReturnTypeHint($tokens, $startIndex)) {
                continue;
            }
            if ($tokens[$startIndex]->equals(';')) {
                // No function body defined, fallback to PHPDoc.
                if ($this->hasVoidReturnAnnotation($tokens, $index)) {
                    $this->fixFunctionDefinition($tokens, $startIndex);
                }
                continue;
            }
            if ($this->hasReturnAnnotation($tokens, $index)) {
                continue;
            }
            $endIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);
            if ($this->hasVoidReturn($tokens, $startIndex, $endIndex)) {
                $this->fixFunctionDefinition($tokens, $startIndex);
            }
        }
    }
    /**
     * Determine whether there is a non-void return annotation in the function's PHPDoc comment.
     *
     * @param int $index The index of the function token
     *
     * @return bool
     */
    private function hasReturnAnnotation(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        foreach ($this->findReturnAnnotations($tokens, $index) as $return) {
            if (['void'] !== $return->getTypes()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Determine whether there is a void return annotation in the function's PHPDoc comment.
     *
     * @param int $index The index of the function token
     *
     * @return bool
     */
    private function hasVoidReturnAnnotation(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        foreach ($this->findReturnAnnotations($tokens, $index) as $return) {
            if (['void'] === $return->getTypes()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Determine whether the function already has a return type hint.
     *
     * @param int $index The index of the end of the function definition line, EG at { or ;
     *
     * @return bool
     */
    private function hasReturnTypeHint(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $endFuncIndex = $tokens->getPrevTokenOfKind($index, [')']);
        $nextIndex = $tokens->getNextMeaningfulToken($endFuncIndex);
        return $tokens[$nextIndex]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON);
    }
    /**
     * Determine whether the function has a void return.
     *
     * @param int $startIndex Start of function body
     * @param int $endIndex   End of function body
     *
     * @return bool
     */
    private function hasVoidReturn(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($i = $startIndex; $i < $endIndex; ++$i) {
            if ($tokens[$i]->isGivenKind(\T_CLASS) && $tokensAnalyzer->isAnonymousClass($i) || $tokens[$i]->isGivenKind(\T_FUNCTION) && $tokensAnalyzer->isLambda($i)) {
                $i = $tokens->getNextTokenOfKind($i, ['{']);
                $i = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $i);
                continue;
            }
            if ($tokens[$i]->isGivenKind([\T_YIELD, \T_YIELD_FROM])) {
                return \false;
                // Generators cannot return void.
            }
            if (!$tokens[$i]->isGivenKind(\T_RETURN)) {
                continue;
            }
            $i = $tokens->getNextMeaningfulToken($i);
            if (!$tokens[$i]->equals(';')) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * @param int $index The index of the end of the function definition line, EG at { or ;
     */
    private function fixFunctionDefinition(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $endFuncIndex = $tokens->getPrevTokenOfKind($index, [')']);
        $tokens->insertAt($endFuncIndex + 1, [new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON, ':']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_STRING, 'void'])]);
    }
    /**
     * Find all the return annotations in the function's PHPDoc comment.
     *
     * @param int $index The index of the function token
     *
     * @return Annotation[]
     */
    private function findReturnAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([\T_ABSTRACT, \T_FINAL, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \T_STATIC]));
        if (!$tokens[$index]->isGivenKind(\T_DOC_COMMENT)) {
            return [];
        }
        $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($tokens[$index]->getContent());
        return $doc->getAnnotationsOfType('return');
    }
}
