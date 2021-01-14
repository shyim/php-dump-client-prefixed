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
namespace _PhpScoper3fe455fa007d\PhpCsFixer;

use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 */
abstract class AbstractPhpdocToTypeDeclarationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * @var array<string, bool>
     */
    private static $syntaxValidationCache = [];
    protected final function isValidSyntax($code)
    {
        if (!isset(self::$syntaxValidationCache[$code])) {
            try {
                \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($code);
                self::$syntaxValidationCache[$code] = \true;
            } catch (\ParseError $e) {
                self::$syntaxValidationCache[$code] = \false;
            }
        }
        return self::$syntaxValidationCache[$code];
    }
}
