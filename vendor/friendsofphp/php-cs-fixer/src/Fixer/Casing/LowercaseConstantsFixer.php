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

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * Fixer for rules defined in PSR2 ¶2.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @deprecated proxy to ConstantCaseFixer
 */
final class LowercaseConstantsFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('The PHP constants `true`, `false`, and `null` MUST be in lower case.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n")]);
    }
    /**
     * Returns names of fixers to use instead, if any.
     *
     * @return string[]
     */
    public function getSuccessorsNames()
    {
        return \array_keys($this->proxyFixers);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        $fixer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Casing\ConstantCaseFixer();
        $fixer->configure(['case' => 'lower']);
        return [$fixer];
    }
}
