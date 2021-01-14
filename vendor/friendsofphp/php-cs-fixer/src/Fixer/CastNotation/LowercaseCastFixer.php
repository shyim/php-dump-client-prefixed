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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\CastNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class LowercaseCastFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Cast should be written in lower case.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
    $a = (BOOLEAN) $b;
    $a = (BOOL) $b;
    $a = (INTEGER) $b;
    $a = (INT) $b;
    $a = (DOUBLE) $b;
    $a = (FLoaT) $b;
    $a = (reaL) $b;
    $a = (flOAT) $b;
    $a = (sTRING) $b;
    $a = (ARRAy) $b;
    $a = (OBJect) $b;
    $a = (UNset) $b;
    $a = (Binary) $b;
', new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(null, 70399)), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
    $a = (BOOLEAN) $b;
    $a = (BOOL) $b;
    $a = (INTEGER) $b;
    $a = (INT) $b;
    $a = (DOUBLE) $b;
    $a = (FLoaT) $b;
    $a = (flOAT) $b;
    $a = (sTRING) $b;
    $a = (ARRAy) $b;
    $a = (OBJect) $b;
    $a = (UNset) $b;
    $a = (Binary) $b;
', new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70400))]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token::getCastTokenKinds());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isCast()) {
                continue;
            }
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), \strtolower($tokens[$index]->getContent())]);
        }
    }
}
