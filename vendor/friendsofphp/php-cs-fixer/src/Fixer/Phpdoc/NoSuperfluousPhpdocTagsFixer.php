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
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation;
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
final class NoSuperfluousPhpdocTagsFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Removes `@param`, `@return` and `@var` tags that don\'t provide any useful information.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     */
    public function doFoo(Bar $bar, $baz) {}
}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     */
    public function doFoo(Bar $bar, $baz) {}
}
', ['allow_mixed' => \true]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     *
     * @return Baz
     */
    public function doFoo(Bar $bar, $baz): Baz {}
}
', new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(70000)), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo(Bar $bar, $baz) {}
}
', ['remove_inheritdoc' => \true]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     * @param string|int|null $qux
     */
    public function doFoo(Bar $bar, $baz /*, $qux = null */) {}
}
', ['allow_unused_params' => \true])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpdocAlignFixer, VoidReturnFixer.
     * Must run after CommentToPhpdocFixer, FullyQualifiedStrictTypesFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocIndentFixer, PhpdocReturnSelfReferenceFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocToParamTypeFixer, PhpdocToReturnTypeFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return 6;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $namespaceUseAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer();
        $shortNames = [];
        foreach ($namespaceUseAnalyzer->getDeclarationsFromTokens($tokens) as $namespaceUseAnalysis) {
            $shortNames[\strtolower($namespaceUseAnalysis->getShortName())] = '\\' . \strtolower($namespaceUseAnalysis->getFullName());
        }
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $content = $initialContent = $token->getContent();
            $documentedElementIndex = $this->findDocumentedElement($tokens, $index);
            if (null === $documentedElementIndex) {
                continue;
            }
            $token = $tokens[$documentedElementIndex];
            if ($this->configuration['remove_inheritdoc']) {
                $content = $this->removeSuperfluousInheritDoc($content);
            }
            if ($token->isGivenKind(\T_FUNCTION)) {
                $content = $this->fixFunctionDocComment($content, $tokens, $index, $shortNames);
            } elseif ($token->isGivenKind(\T_VARIABLE)) {
                $content = $this->fixPropertyDocComment($content, $tokens, $index, $shortNames);
            }
            if ('' === $content) {
                $content = '/**  */';
            }
            if ($content !== $initialContent) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $content]);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('allow_mixed', 'Whether type `mixed` without description is allowed (`true`) or considered superfluous (`false`)'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('remove_inheritdoc', 'Remove `@inheritDoc` tags'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption(), (new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('allow_unused_params', 'Whether `param` annotation without actual signature is allowed (`true`) or considered superfluous (`false`)'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption()]);
    }
    /**
     * @param int $docCommentIndex
     *
     * @return null|int
     */
    private function findDocumentedElement(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $docCommentIndex)
    {
        $index = $docCommentIndex;
        do {
            $index = $tokens->getNextMeaningfulToken($index);
            if (null === $index || $tokens[$index]->isGivenKind([\T_FUNCTION, \T_CLASS, \T_INTERFACE])) {
                return $index;
            }
        } while ($tokens[$index]->isGivenKind([\T_ABSTRACT, \T_FINAL, \T_STATIC, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC]));
        $index = $tokens->getNextMeaningfulToken($docCommentIndex);
        $kindsBeforeProperty = [\T_STATIC, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT, \T_STRING, \T_NS_SEPARATOR];
        if (!$tokens[$index]->isGivenKind($kindsBeforeProperty)) {
            return null;
        }
        do {
            $index = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$index]->isGivenKind(\T_VARIABLE)) {
                return $index;
            }
        } while ($tokens[$index]->isGivenKind($kindsBeforeProperty));
        return null;
    }
    /**
     * @param string $content
     * @param int    $functionIndex
     *
     * @return string
     */
    private function fixFunctionDocComment($content, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $functionIndex, array $shortNames)
    {
        $docBlock = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($content);
        $openingParenthesisIndex = $tokens->getNextTokenOfKind($functionIndex, ['(']);
        $closingParenthesisIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openingParenthesisIndex);
        $argumentsInfo = $this->getArgumentsInfo($tokens, $openingParenthesisIndex + 1, $closingParenthesisIndex - 1);
        foreach ($docBlock->getAnnotationsOfType('param') as $annotation) {
            if (0 === \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match('/@param(?:\\s+[^\\$]\\S+)?\\s+(\\$\\S+)/', $annotation->getContent(), $matches)) {
                continue;
            }
            $argumentName = $matches[1];
            if (!isset($argumentsInfo[$argumentName]) && $this->configuration['allow_unused_params']) {
                continue;
            }
            if (!isset($argumentsInfo[$argumentName]) || $this->annotationIsSuperfluous($annotation, $argumentsInfo[$argumentName], $shortNames)) {
                $annotation->remove();
            }
        }
        $returnTypeInfo = $this->getReturnTypeInfo($tokens, $closingParenthesisIndex);
        foreach ($docBlock->getAnnotationsOfType('return') as $annotation) {
            if ($this->annotationIsSuperfluous($annotation, $returnTypeInfo, $shortNames)) {
                $annotation->remove();
            }
        }
        return $docBlock->getContent();
    }
    /**
     * @param string $content
     * @param int    $index   Index of the DocComment token
     *
     * @return string
     */
    private function fixPropertyDocComment($content, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index, array $shortNames)
    {
        $docBlock = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($content);
        do {
            $index = $tokens->getNextMeaningfulToken($index);
        } while ($tokens[$index]->isGivenKind([\T_STATIC, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC]));
        $propertyTypeInfo = $this->getPropertyTypeInfo($tokens, $index);
        foreach ($docBlock->getAnnotationsOfType('var') as $annotation) {
            if ($this->annotationIsSuperfluous($annotation, $propertyTypeInfo, $shortNames)) {
                $annotation->remove();
            }
        }
        return $docBlock->getContent();
    }
    /**
     * @param int $start
     * @param int $end
     *
     * @return array<string, array>
     */
    private function getArgumentsInfo(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $start, $end)
    {
        $argumentsInfo = [];
        for ($index = $start; $index <= $end; ++$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_VARIABLE)) {
                continue;
            }
            $beforeArgumentIndex = $tokens->getPrevTokenOfKind($index, ['(', ',']);
            $typeIndex = $tokens->getNextMeaningfulToken($beforeArgumentIndex);
            if ($typeIndex !== $index) {
                $info = $this->parseTypeHint($tokens, $typeIndex);
            } else {
                $info = ['type' => null, 'allows_null' => \true];
            }
            if (!$info['allows_null']) {
                $nextIndex = $tokens->getNextMeaningfulToken($index);
                if ($tokens[$nextIndex]->equals('=') && $tokens[$tokens->getNextMeaningfulToken($nextIndex)]->equals([\T_STRING, 'null'])) {
                    $info['allows_null'] = \true;
                }
            }
            $argumentsInfo[$token->getContent()] = $info;
        }
        return $argumentsInfo;
    }
    private function getReturnTypeInfo(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $closingParenthesisIndex)
    {
        $colonIndex = $tokens->getNextMeaningfulToken($closingParenthesisIndex);
        if ($tokens[$colonIndex]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON)) {
            return $this->parseTypeHint($tokens, $tokens->getNextMeaningfulToken($colonIndex));
        }
        return ['type' => null, 'allows_null' => \true];
    }
    /**
     * @param int $index The index of the first token of the type hint
     *
     * @return array
     */
    private function getPropertyTypeInfo(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if ($tokens[$index]->isGivenKind(\T_VARIABLE)) {
            return ['type' => null, 'allows_null' => \true];
        }
        return $this->parseTypeHint($tokens, $index);
    }
    /**
     * @param int $index The index of the first token of the type hint
     *
     * @return array
     */
    private function parseTypeHint(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $allowsNull = \false;
        if ($tokens[$index]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE)) {
            $allowsNull = \true;
            $index = $tokens->getNextMeaningfulToken($index);
        }
        $type = '';
        while ($tokens[$index]->isGivenKind([\T_NS_SEPARATOR, \T_STRING, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT, \T_CALLABLE])) {
            $type .= $tokens[$index]->getContent();
            $index = $tokens->getNextMeaningfulToken($index);
        }
        return ['type' => '' === $type ? null : $type, 'allows_null' => $allowsNull];
    }
    /**
     * @param array<string, string> $symbolShortNames
     *
     * @return bool
     */
    private function annotationIsSuperfluous(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation $annotation, array $info, array $symbolShortNames)
    {
        if ('param' === $annotation->getTag()->getName()) {
            $regex = '/@param\\s+(?:\\S|\\s(?!\\$))++\\s\\$\\S+\\s+\\S/';
        } elseif ('var' === $annotation->getTag()->getName()) {
            $regex = '/@var\\s+\\S+(\\s+\\$\\S+)?(\\s+)(?!\\*+\\/)([^$\\s]+)/';
        } else {
            $regex = '/@return\\s+\\S+\\s+\\S/';
        }
        if (\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::match($regex, $annotation->getContent())) {
            return \false;
        }
        $annotationTypes = $this->toComparableNames($annotation->getTypes(), $symbolShortNames);
        if (['null'] === $annotationTypes) {
            return \false;
        }
        if (['mixed'] === $annotationTypes && null === $info['type']) {
            return !$this->configuration['allow_mixed'];
        }
        $actualTypes = null === $info['type'] ? [] : [$info['type']];
        if ($info['allows_null']) {
            $actualTypes[] = 'null';
        }
        return $annotationTypes === $this->toComparableNames($actualTypes, $symbolShortNames);
    }
    /**
     * Normalizes types to make them comparable.
     *
     * Converts given types to lowercase, replaces imports aliases with
     * their matching FQCN, and finally sorts the result.
     *
     * @param string[]              $types            The types to normalize
     * @param array<string, string> $symbolShortNames The imports aliases
     *
     * @return array The normalized types
     */
    private function toComparableNames(array $types, array $symbolShortNames)
    {
        $normalized = \array_map(static function ($type) use($symbolShortNames) {
            $type = \strtolower($type);
            if (isset($symbolShortNames[$type])) {
                return $symbolShortNames[$type];
            }
            return $type;
        }, $types);
        \sort($normalized);
        return $normalized;
    }
    /**
     * @param string $docComment
     *
     * @return string
     */
    private function removeSuperfluousInheritDoc($docComment)
    {
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('~
            # $1: before @inheritDoc tag
            (
                # beginning of comment or a PHPDoc tag
                (?:
                    ^/\\*\\*
                    (?:
                        \\R
                        [ \\t]*(?:\\*[ \\t]*)?
                    )*?
                    |
                    @\\N+
                )

                # empty comment lines
                (?:
                    \\R
                    [ \\t]*(?:\\*[ \\t]*?)?
                )*
            )

            # spaces before @inheritDoc tag
            [ \\t]*

            # @inheritDoc tag
            (?:@inheritDocs?|\\{@inheritDocs?\\})

            # $2: after @inheritDoc tag
            (
                # empty comment lines
                (?:
                    \\R
                    [ \\t]*(?:\\*[ \\t]*)?
                )*

                # a PHPDoc tag or end of comment
                (?:
                    @\\N+
                    |
                    (?:
                        \\R
                        [ \\t]*(?:\\*[ \\t]*)?
                    )*
                    [ \\t]*\\*/$
                )
            )
        ~ix', '$1$2', $docComment);
    }
}
