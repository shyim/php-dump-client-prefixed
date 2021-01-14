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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
final class OrderedTraitsFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Trait `use` statements must be sorted alphabetically.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php class Foo { \nuse Z; use A; }\n")], null, 'Risky when depending on order of the imports.');
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT);
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($this->findUseStatementsGroups($tokens) as $uses) {
            $this->sortUseStatements($tokens, $uses);
        }
    }
    /**
     * @return iterable<array<int, Tokens>>
     */
    private function findUseStatementsGroups(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $uses = [];
        for ($index = 1, $max = \count($tokens); $index < $max; ++$index) {
            $token = $tokens[$index];
            if ($token->isWhitespace() || $token->isComment()) {
                continue;
            }
            if (!$token->isGivenKind(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT)) {
                if (\count($uses) > 0) {
                    (yield $uses);
                    $uses = [];
                }
                continue;
            }
            $endIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
            if ($tokens[$endIndex]->equals('{')) {
                $endIndex = $tokens->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $endIndex);
            }
            $use = [];
            for ($i = $index; $i <= $endIndex; ++$i) {
                $use[] = $tokens[$i];
            }
            $uses[$index] = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromArray($use);
            $index = $endIndex;
        }
    }
    /**
     * @param array<int, Tokens> $uses
     */
    private function sortUseStatements(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $uses)
    {
        foreach ($uses as $use) {
            $this->sortMultipleTraitsInStatement($use);
        }
        $this->sort($tokens, $uses);
    }
    private function sortMultipleTraitsInStatement(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $use)
    {
        $traits = [];
        $indexOfName = null;
        $name = [];
        for ($index = 0, $max = \count($use); $index < $max; ++$index) {
            $token = $use[$index];
            if ($token->isGivenKind([\T_STRING, \T_NS_SEPARATOR])) {
                $name[] = $token;
                if (null === $indexOfName) {
                    $indexOfName = $index;
                }
                continue;
            }
            if ($token->equalsAny([',', ';', '{'])) {
                $traits[$indexOfName] = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromArray($name);
                $name = [];
                $indexOfName = null;
            }
            if ($token->equals('{')) {
                $index = $use->findBlockEnd(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
            }
        }
        $this->sort($use, $traits);
    }
    /**
     * @param array<int, Tokens> $elements
     */
    private function sort(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens, array $elements)
    {
        /**
         * @return string
         */
        $toTraitName = static function (\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $use) {
            $string = '';
            foreach ($use as $token) {
                if ($token->equalsAny([';', '{'])) {
                    break;
                }
                if ($token->isGivenKind([\T_NS_SEPARATOR, \T_STRING])) {
                    $string .= $token->getContent();
                }
            }
            return \ltrim($string, '\\');
        };
        $sortedElements = $elements;
        \uasort($sortedElements, static function (\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $useA, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $useB) use($toTraitName) {
            return \strcasecmp($toTraitName($useA), $toTraitName($useB));
        });
        $sortedElements = \array_combine(\array_keys($elements), \array_values($sortedElements));
        foreach (\array_reverse($sortedElements, \true) as $index => $tokensToInsert) {
            $tokens->overrideRange($index, $index + \count($elements[$index]) - 1, $tokensToInsert);
        }
    }
}
