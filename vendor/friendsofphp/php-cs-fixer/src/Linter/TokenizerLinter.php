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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Linter;

use _PhpScoper3fe455fa007d\PhpCsFixer\FileReader;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CodeHasher;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * Handle PHP code linting.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TokenizerLinter implements \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\LinterInterface
{
    public function __construct()
    {
        if (\false === \defined('TOKEN_PARSE') || \false === \class_exists(\CompileError::class)) {
            throw new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\UnavailableLinterException('Cannot use tokenizer as linter.');
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isAsync()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return $this->lintSource(\_PhpScoper3fe455fa007d\PhpCsFixer\FileReader::createSingleton()->read($path));
    }
    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        try {
            // To lint, we will parse the source into Tokens.
            // During that process, it might throw a ParseError or CompileError.
            // If it won't, cache of tokenized version of source will be kept, which is great for Runner.
            // Yet, first we need to clear already existing cache to not hit it and lint the code indeed.
            $codeHash = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CodeHasher::calculateCodeHash($source);
            \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::clearCache($codeHash);
            \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($source);
            return new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\TokenizerLintingResult();
        } catch (\ParseError $e) {
            return new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\TokenizerLintingResult($e);
        } catch (\CompileError $e) {
            return new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\TokenizerLintingResult($e);
        }
    }
}
