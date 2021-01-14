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

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @author SpacePossum
 *
 * @deprecated in 2.8, proxy to ClassAttributesSeparationFixer
 */
final class MethodSeparationFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Methods must be separated with one blank line.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Sample
{
    protected function foo()
    {
    }
    protected function bar()
    {
    }
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, IndentationTypeFixer.
     * Must run after OrderedClassElementsFixer.
     */
    public function getPriority()
    {
        return parent::getPriority();
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
        $fixer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer();
        $fixer->configure(['elements' => ['method' => \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::SPACING_ONE]]);
        return [$fixer];
    }
}
