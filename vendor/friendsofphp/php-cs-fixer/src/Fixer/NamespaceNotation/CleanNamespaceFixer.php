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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\NamespaceNotation;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractLinesBeforeNamespaceFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
final class CleanNamespaceFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractLinesBeforeNamespaceFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $samples = [];
        foreach (['namespace Foo \\ Bar;', 'echo foo /* comment */ \\ bar();'] as $sample) {
            $samples[] = new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\n" . $sample . "\n", new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecification(null, 80000 - 1));
        }
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Namespace must not contain spacing, comments or PHPDoc.', $samples);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \PHP_VERSION_ID < 80000 && $tokens->isTokenKindFound(\T_NS_SEPARATOR);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $count = $tokens->count();
        for ($index = 0; $index < $count; ++$index) {
            if ($tokens[$index]->isGivenKind(\T_NS_SEPARATOR)) {
                $previousIndex = $tokens->getPrevMeaningfulToken($index);
                $index = $this->fixNamespace($tokens, $tokens[$previousIndex]->isGivenKind(\T_STRING) ? $previousIndex : $index);
            }
        }
    }
    /**
     * @param int $index start of namespace
     *
     * @return int
     */
    private function fixNamespace(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $tillIndex = $index;
        // go to the end of the namespace
        while ($tokens[$tillIndex]->isGivenKind([\T_NS_SEPARATOR, \T_STRING])) {
            $tillIndex = $tokens->getNextMeaningfulToken($tillIndex);
        }
        $tillIndex = $tokens->getPrevMeaningfulToken($tillIndex);
        $spaceIndexes = [];
        for (; $index <= $tillIndex; ++$index) {
            if ($tokens[$index]->isGivenKind(\T_WHITESPACE)) {
                $spaceIndexes[] = $index;
            } elseif ($tokens[$index]->isComment()) {
                $tokens->clearAt($index);
            }
        }
        if ($tokens[$index - 1]->isWhiteSpace()) {
            \array_pop($spaceIndexes);
        }
        foreach ($spaceIndexes as $i) {
            $tokens->clearAt($i);
        }
        return $index;
    }
}
