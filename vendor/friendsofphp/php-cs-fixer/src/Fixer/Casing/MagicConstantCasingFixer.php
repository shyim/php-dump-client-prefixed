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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Casing;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author ntzm
 */
final class MagicConstantCasingFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Magic constants should be referred to using the correct casing.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\necho __dir__;\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound($this->getMagicConstantTokens());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $magicConstants = $this->getMagicConstants();
        $magicConstantTokens = $this->getMagicConstantTokens();
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($magicConstantTokens)) {
                $tokens[$index] = new \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Token([$token->getId(), $magicConstants[$token->getId()]]);
            }
        }
    }
    /**
     * @return array<int, string>
     */
    private function getMagicConstants()
    {
        static $magicConstants = null;
        if (null === $magicConstants) {
            $magicConstants = [\T_LINE => '__LINE__', \T_FILE => '__FILE__', \T_DIR => '__DIR__', \T_FUNC_C => '__FUNCTION__', \T_CLASS_C => '__CLASS__', \T_METHOD_C => '__METHOD__', \T_NS_C => '__NAMESPACE__', \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\CT::T_CLASS_CONSTANT => 'class', \T_TRAIT_C => '__TRAIT__'];
        }
        return $magicConstants;
    }
    /**
     * @return array<int>
     */
    private function getMagicConstantTokens()
    {
        static $magicConstantTokens = null;
        if (null === $magicConstantTokens) {
            $magicConstantTokens = \array_keys($this->getMagicConstants());
        }
        return $magicConstantTokens;
    }
}
