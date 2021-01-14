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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 */
abstract class AbstractPhpUnitFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public final function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_STRING]);
    }
    protected final function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $phpUnitTestCaseIndicator = new \_PhpScoper3fe455fa007d\PhpCsFixer\Indicator\PhpUnitTestCaseIndicator();
        foreach ($phpUnitTestCaseIndicator->findPhpUnitClasses($tokens) as $indices) {
            $this->applyPhpUnitClassFix($tokens, $indices[0], $indices[1]);
        }
    }
    /**
     * @param int $startIndex
     * @param int $endIndex
     */
    protected abstract function applyPhpUnitClassFix(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex);
    /**
     * @param int $index
     *
     * @return int
     */
    protected final function getDocBlockIndex(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT]));
        return $index;
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    protected final function isPHPDoc(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        return $tokens[$index]->isGivenKind(\T_DOC_COMMENT);
    }
}
