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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Import;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Transforms imported FQCN parameters and return types in function arguments to short version.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php

use Foo\\Bar;

class SomeClass
{
    public function doSomething(\\Foo\\Bar $foo)
    {
    }
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

use Foo\\Bar;
use Foo\\Bar\\Baz;

class SomeClass
{
    public function doSomething(\\Foo\\Bar $foo): \\Foo\\Bar\\Baz
    {
    }
}
', new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70000))]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer.
     * Must run after PhpdocToReturnTypeFixer.
     */
    public function getPriority()
    {
        return 7;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_FUNCTION) && (\count((new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer())->getDeclarations($tokens)) || \count((new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens)));
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $lastIndex = $tokens->count() - 1;
        for ($index = $lastIndex; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            // Return types are only available since PHP 7.0
            $this->fixFunctionReturnType($tokens, $index);
            $this->fixFunctionArguments($tokens, $index);
        }
    }
    /**
     * @param int $index
     */
    private function fixFunctionArguments(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $arguments = (new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer())->getFunctionArguments($tokens, $index);
        foreach ($arguments as $argument) {
            if (!$argument->hasTypeAnalysis()) {
                continue;
            }
            $this->detectAndReplaceTypeWithShortType($tokens, $argument->getTypeAnalysis());
        }
    }
    /**
     * @param int $index
     */
    private function fixFunctionReturnType(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if (\PHP_VERSION_ID < 70000) {
            return;
        }
        $returnType = (new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer())->getFunctionReturnType($tokens, $index);
        if (!$returnType) {
            return;
        }
        $this->detectAndReplaceTypeWithShortType($tokens, $returnType);
    }
    private function detectAndReplaceTypeWithShortType(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis $type)
    {
        if ($type->isReservedType()) {
            return;
        }
        $typeName = $type->getName();
        if (0 !== \strpos($typeName, '\\')) {
            return;
        }
        $shortType = (new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver())->resolve($tokens, $typeName);
        if ($shortType === $typeName) {
            return;
        }
        $shortType = (new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator())->generate($shortType);
        if (\true === $type->isNullable()) {
            \array_unshift($shortType, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE, '?']));
        }
        $tokens->overrideRange($type->getStartIndex(), $type->getEndIndex(), $shortType);
    }
}
