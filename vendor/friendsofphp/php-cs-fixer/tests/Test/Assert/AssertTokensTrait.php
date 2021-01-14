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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Tests\Test\Assert;

use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
trait AssertTokensTrait
{
    private static function assertTokens(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $expectedTokens, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $inputTokens)
    {
        foreach ($expectedTokens as $index => $expectedToken) {
            if (!isset($inputTokens[$index])) {
                static::fail(\sprintf("The token at index %d must be:\n%s, but is not set in the input collection.", $index, $expectedToken->toJson()));
            }
            $inputToken = $inputTokens[$index];
            static::assertTrue($expectedToken->equals($inputToken), \sprintf("The token at index %d must be:\n%s,\ngot:\n%s.", $index, $expectedToken->toJson(), $inputToken->toJson()));
            $expectedTokenKind = $expectedToken->isArray() ? $expectedToken->getId() : $expectedToken->getContent();
            static::assertTrue($inputTokens->isTokenKindFound($expectedTokenKind), \sprintf('The token kind %s (%s) must be found in tokens collection.', $expectedTokenKind, \is_string($expectedTokenKind) ? $expectedTokenKind : \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token::getNameForId($expectedTokenKind)));
        }
        static::assertSame($expectedTokens->count(), $inputTokens->count(), 'Both collections must have the same length.');
    }
}
