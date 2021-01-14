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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ClassNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use _PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options;
/**
 * Make sure there is one blank line above and below class elements.
 *
 * The exception is when an element is the first or last item in a 'classy'.
 *
 * @author SpacePossum
 */
final class ClassAttributesSeparationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    const SPACING_NONE = 'none';
    /**
     * @internal
     */
    const SPACING_ONE = 'one';
    /**
     * @internal
     */
    const SUPPORTED_SPACINGS = [self::SPACING_NONE, self::SPACING_ONE];
    /**
     * @internal
     */
    const SUPPORTED_TYPES = ['const', 'method', 'property'];
    /**
     * @var array<string, string>
     */
    private $classElementTypes = [];
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->classElementTypes = [];
        // reset previous configuration
        foreach ($this->configuration['elements'] as $elementType => $spacing) {
            $this->classElementTypes[$elementType] = $spacing;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Class, trait and interface elements must be separated with one or none blank line.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Sample
{
    protected function foo()
    {
    }
    protected function bar()
    {
    }


}
'), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Sample
{private $a; // a is awesome
    /** second in a hour */
    private $b;
}
', ['elements' => ['property' => self::SPACING_ONE]]), new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class Sample
{
    const A = 1;
    /** seconds in some hours */
    const B = 3600;
}
', ['elements' => ['const' => self::SPACING_ONE]])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, IndentationTypeFixer.
     * Must run after OrderedClassElementsFixer, SingleClassElementPerStatementFixer.
     */
    public function getPriority()
    {
        return 55;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token::getClassyTokenKinds());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokensAnalyzer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $class = $classStart = $classEnd = \false;
        foreach (\array_reverse($tokensAnalyzer->getClassyElements(), \true) as $index => $element) {
            if (!isset($this->classElementTypes[$element['type']])) {
                continue;
                // not configured to be fixed
            }
            $spacing = $this->classElementTypes[$element['type']];
            if ($element['classIndex'] !== $class) {
                $class = $element['classIndex'];
                $classStart = $tokens->getNextTokenOfKind($class, ['{']);
                $classEnd = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $classStart);
            }
            if ('method' === $element['type'] && !$tokens[$class]->isGivenKind(\T_INTERFACE)) {
                // method of class or trait
                $attributes = $tokensAnalyzer->getMethodAttributes($index);
                $methodEnd = \true === $attributes['abstract'] ? $tokens->getNextTokenOfKind($index, [';']) : $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $tokens->getNextTokenOfKind($index, ['{']));
                $this->fixSpaceBelowClassMethod($tokens, $classEnd, $methodEnd, $spacing);
                $this->fixSpaceAboveClassElement($tokens, $classStart, $index, $spacing);
                continue;
            }
            // `const`, `property` or `method` of an `interface`
            $this->fixSpaceBelowClassElement($tokens, $classEnd, $tokens->getNextTokenOfKind($index, [';']), $spacing);
            $this->fixSpaceAboveClassElement($tokens, $classStart, $index, $spacing);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('elements', 'Dictionary of `const|method|property` => `none|one` values.'))->setNormalizer(static function (\_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Options $options, $values) {
            $deprecated = \array_intersect($values, self::SUPPORTED_TYPES);
            if (\count($deprecated) > 0) {
                $message = 'A list of elements is deprecated, use a dictionary of `const|method|property` => `none|one` instead.';
                @\trigger_error($message, \E_USER_DEPRECATED);
                return \array_fill_keys($deprecated, self::SPACING_ONE);
            }
            return $values;
        })->setAllowedTypes(['array'])->setAllowedValues([static function ($option) {
            $deprecated = \array_intersect($option, self::SUPPORTED_TYPES);
            if (\count($deprecated) > 0) {
                $option = \array_fill_keys($deprecated, self::SPACING_ONE);
            }
            foreach ($option as $type => $spacing) {
                if (!\in_array($type, self::SUPPORTED_TYPES, \true)) {
                    throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Unexpected element type, expected any of "%s", got "%s".', \implode('", "', self::SUPPORTED_TYPES), \is_object($type) ? \get_class($type) : \gettype($type) . '#' . $type));
                }
                if (!\in_array($spacing, self::SUPPORTED_SPACINGS, \true)) {
                    throw new \_PhpScoper3fe455fa007d\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Unexpected spacing for element type "%s", expected any of "%s", got "%s".', $spacing, \implode('", "', self::SUPPORTED_SPACINGS), \is_object($spacing) ? \get_class($spacing) : (null === $spacing ? 'null' : \gettype($spacing) . '#' . $spacing)));
                }
            }
            return \true;
        }])->setDefault(['const' => self::SPACING_ONE, 'method' => self::SPACING_ONE, 'property' => self::SPACING_ONE])->getOption()]);
    }
    /**
     * Fix spacing below an element of a class, interface or trait.
     *
     * Deals with comments, PHPDocs and spaces above the element with respect to the position of the
     * element within the class, interface or trait.
     *
     * @param int    $classEndIndex
     * @param int    $elementEndIndex
     * @param string $spacing
     */
    private function fixSpaceBelowClassElement(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classEndIndex, $elementEndIndex, $spacing)
    {
        for ($nextNotWhite = $elementEndIndex + 1;; ++$nextNotWhite) {
            if (($tokens[$nextNotWhite]->isComment() || $tokens[$nextNotWhite]->isWhitespace()) && \false === \strpos($tokens[$nextNotWhite]->getContent(), "\n")) {
                continue;
            }
            break;
        }
        if ($tokens[$nextNotWhite]->isWhitespace()) {
            $nextNotWhite = $tokens->getNextNonWhitespace($nextNotWhite);
        }
        if ($tokens[$nextNotWhite]->isGivenKind(\T_FUNCTION)) {
            $this->correctLineBreaks($tokens, $elementEndIndex, $nextNotWhite, 2);
            return;
        }
        $this->correctLineBreaks($tokens, $elementEndIndex, $nextNotWhite, $nextNotWhite === $classEndIndex || self::SPACING_NONE === $spacing ? 1 : 2);
    }
    /**
     * Fix spacing below a method of a class or trait.
     *
     * Deals with comments, PHPDocs and spaces above the method with respect to the position of the
     * method within the class or trait.
     *
     * @param int    $classEndIndex
     * @param int    $elementEndIndex
     * @param string $spacing
     */
    private function fixSpaceBelowClassMethod(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classEndIndex, $elementEndIndex, $spacing)
    {
        $nextNotWhite = $tokens->getNextNonWhitespace($elementEndIndex);
        $this->correctLineBreaks($tokens, $elementEndIndex, $nextNotWhite, $nextNotWhite === $classEndIndex || self::SPACING_NONE === $spacing ? 1 : 2);
    }
    /**
     * Fix spacing above an element of a class, interface or trait.
     *
     * Deals with comments, PHPDocs and spaces above the element with respect to the position of the
     * element within the class, interface or trait.
     *
     * @param int    $classStartIndex index of the class Token the element is in
     * @param int    $elementIndex    index of the element to fix
     * @param string $spacing
     */
    private function fixSpaceAboveClassElement(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $classStartIndex, $elementIndex, $spacing)
    {
        static $methodAttr = [\T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \T_ABSTRACT, \T_FINAL, \T_STATIC, \T_STRING, \T_NS_SEPARATOR, \T_VAR, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT];
        $nonWhiteAbove = null;
        // find out where the element definition starts
        $firstElementAttributeIndex = $elementIndex;
        for ($i = $elementIndex; $i > $classStartIndex; --$i) {
            $nonWhiteAbove = $tokens->getNonWhitespaceSibling($i, -1);
            if (null !== $nonWhiteAbove && $tokens[$nonWhiteAbove]->isGivenKind($methodAttr)) {
                $firstElementAttributeIndex = $nonWhiteAbove;
            } else {
                break;
            }
        }
        // deal with comments above a element
        if ($tokens[$nonWhiteAbove]->isGivenKind(\T_COMMENT)) {
            if (1 === $firstElementAttributeIndex - $nonWhiteAbove) {
                // no white space found between comment and element start
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, 1);
                return;
            }
            // $tokens[$nonWhiteAbove+1] is always a white space token here
            if (\substr_count($tokens[$nonWhiteAbove + 1]->getContent(), "\n") > 1) {
                // more than one line break, always bring it back to 2 line breaks between the element start and what is above it
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, 2);
                return;
            }
            // there are 2 cases:
            if ($tokens[$nonWhiteAbove - 1]->isWhitespace() && \substr_count($tokens[$nonWhiteAbove - 1]->getContent(), "\n") > 0) {
                // 1. The comment is meant for the element (although not a PHPDoc),
                //    make sure there is one line break between the element and the comment...
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, 1);
                //    ... and make sure there is blank line above the comment (with the exception when it is directly after a class opening)
                $nonWhiteAbove = $this->findCommentBlockStart($tokens, $nonWhiteAbove);
                $nonWhiteAboveComment = $tokens->getNonWhitespaceSibling($nonWhiteAbove, -1);
                $this->correctLineBreaks($tokens, $nonWhiteAboveComment, $nonWhiteAbove, $nonWhiteAboveComment === $classStartIndex ? 1 : 2);
            } else {
                // 2. The comment belongs to the code above the element,
                //    make sure there is a blank line above the element (i.e. 2 line breaks)
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, 2);
            }
            return;
        }
        // deal with element with a PHPDoc above it
        if ($tokens[$nonWhiteAbove]->isGivenKind(\T_DOC_COMMENT)) {
            // there should be one linebreak between the element and the PHPDoc above it
            $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, 1);
            // there should be one blank line between the PHPDoc and whatever is above (with the exception when it is directly after a class opening)
            $nonWhiteAbovePHPDoc = $tokens->getNonWhitespaceSibling($nonWhiteAbove, -1);
            $this->correctLineBreaks($tokens, $nonWhiteAbovePHPDoc, $nonWhiteAbove, $nonWhiteAbovePHPDoc === $classStartIndex ? 1 : 2);
            return;
        }
        // deal with element with an attribute above it
        if ($tokens[$nonWhiteAbove]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ATTRIBUTE_CLOSE)) {
            // there should be one linebreak between the element and the attribute above it
            $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, 1);
            // make sure there is blank line above the comment (with the exception when it is directly after a class opening)
            $nonWhiteAbove = $this->findAttributeBlockStart($tokens, $nonWhiteAbove);
            $nonWhiteAboveComment = $tokens->getNonWhitespaceSibling($nonWhiteAbove, -1);
            $this->correctLineBreaks($tokens, $nonWhiteAboveComment, $nonWhiteAbove, $nonWhiteAboveComment === $classStartIndex ? 1 : 2);
            return;
        }
        $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstElementAttributeIndex, $nonWhiteAbove === $classStartIndex || self::SPACING_NONE === $spacing ? 1 : 2);
    }
    /**
     * @param int $startIndex
     * @param int $endIndex
     * @param int $reqLineCount
     */
    private function correctLineBreaks(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex, $reqLineCount = 2)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        ++$startIndex;
        $numbOfWhiteTokens = $endIndex - $startIndex;
        if (0 === $numbOfWhiteTokens) {
            $tokens->insertAt($startIndex, new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \str_repeat($lineEnding, $reqLineCount)]));
            return;
        }
        $lineBreakCount = $this->getLineBreakCount($tokens, $startIndex, $endIndex);
        if ($reqLineCount === $lineBreakCount) {
            return;
        }
        if ($lineBreakCount < $reqLineCount) {
            $tokens[$startIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \str_repeat($lineEnding, $reqLineCount - $lineBreakCount) . $tokens[$startIndex]->getContent()]);
            return;
        }
        // $lineCount = > $reqLineCount : check the one Token case first since this one will be true most of the time
        if (1 === $numbOfWhiteTokens) {
            $tokens[$startIndex] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\r\\n|\\n/', '', $tokens[$startIndex]->getContent(), $lineBreakCount - $reqLineCount)]);
            return;
        }
        // $numbOfWhiteTokens = > 1
        $toReplaceCount = $lineBreakCount - $reqLineCount;
        for ($i = $startIndex; $i < $endIndex && $toReplaceCount > 0; ++$i) {
            $tokenLineCount = \substr_count($tokens[$i]->getContent(), "\n");
            if ($tokenLineCount > 0) {
                $tokens[$i] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\r\\n|\\n/', '', $tokens[$i]->getContent(), \min($toReplaceCount, $tokenLineCount))]);
                $toReplaceCount -= $tokenLineCount;
            }
        }
    }
    /**
     * @param int $whiteSpaceStartIndex
     * @param int $whiteSpaceEndIndex
     *
     * @return int
     */
    private function getLineBreakCount(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $whiteSpaceStartIndex, $whiteSpaceEndIndex)
    {
        $lineCount = 0;
        for ($i = $whiteSpaceStartIndex; $i < $whiteSpaceEndIndex; ++$i) {
            $lineCount += \substr_count($tokens[$i]->getContent(), "\n");
        }
        return $lineCount;
    }
    /**
     * @param int $commentIndex
     *
     * @return int
     */
    private function findCommentBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $commentIndex)
    {
        $start = $commentIndex;
        for ($i = $commentIndex - 1; $i > 0; --$i) {
            if ($tokens[$i]->isComment()) {
                $start = $i;
                continue;
            }
            if (!$tokens[$i]->isWhitespace() || $this->getLineBreakCount($tokens, $i, $i + 1) > 1) {
                break;
            }
        }
        return $start;
    }
    /**
     * @param int $index attribute close index
     *
     * @return int
     */
    private function findAttributeBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $start = $index = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ATTRIBUTE, $index);
        for ($i = $index - 1; $i > 0; --$i) {
            if ($tokens[$i]->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_ATTRIBUTE_CLOSE)) {
                $start = $i = $tokens->findBlockStart(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ATTRIBUTE, $i);
                continue;
            }
            if (!$tokens[$i]->isWhitespace() || $this->getLineBreakCount($tokens, $i, $i + 1) > 1) {
                break;
            }
        }
        return $start;
    }
}
