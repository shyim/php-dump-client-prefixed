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
use _PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\TagComparator;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class PhpdocSeparationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * Description.
 * @param string $foo
 *
 *
 * @param bool   $bar Bar
 * @throws Exception|RuntimeException
 * @return bool
 */
function fnc($foo, $bar) {}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, GeneralPhpdocAnnotationRemoveFixer, PhpdocIndentFixer, PhpdocNoAccessFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocOrderFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return -3;
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
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $doc = new \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock($token->getContent());
            $this->fixDescription($doc);
            $this->fixAnnotations($doc);
            $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doc->getContent()]);
        }
    }
    /**
     * Make sure the description is separated from the annotations.
     */
    private function fixDescription(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock $doc)
    {
        foreach ($doc->getLines() as $index => $line) {
            if ($line->containsATag()) {
                break;
            }
            if ($line->containsUsefulContent()) {
                $next = $doc->getLine($index + 1);
                if (null !== $next && $next->containsATag()) {
                    $line->addBlank();
                    break;
                }
            }
        }
    }
    /**
     * Make sure the annotations are correctly separated.
     *
     * @return string
     */
    private function fixAnnotations(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock $doc)
    {
        foreach ($doc->getAnnotations() as $index => $annotation) {
            $next = $doc->getAnnotation($index + 1);
            if (null === $next) {
                break;
            }
            if (\true === $next->getTag()->valid()) {
                if (\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\TagComparator::shouldBeTogether($annotation->getTag(), $next->getTag())) {
                    $this->ensureAreTogether($doc, $annotation, $next);
                } else {
                    $this->ensureAreSeparate($doc, $annotation, $next);
                }
            }
        }
        return $doc->getContent();
    }
    /**
     * Force the given annotations to immediately follow each other.
     */
    private function ensureAreTogether(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock $doc, \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation $first, \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation $second)
    {
        $pos = $first->getEnd();
        $final = $second->getStart();
        for ($pos = $pos + 1; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }
    /**
     * Force the given annotations to have one empty line between each other.
     */
    private function ensureAreSeparate(\_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\DocBlock $doc, \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation $first, \_PhpScoper3fe455fa007d\PhpCsFixer\DocBlock\Annotation $second)
    {
        $pos = $first->getEnd();
        $final = $second->getStart() - 1;
        // check if we need to add a line, or need to remove one or more lines
        if ($pos === $final) {
            $doc->getLine($pos)->addBlank();
            return;
        }
        for ($pos = $pos + 1; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }
}
