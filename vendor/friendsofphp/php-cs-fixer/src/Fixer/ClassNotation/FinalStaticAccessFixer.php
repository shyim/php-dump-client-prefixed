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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @author ntzm
 *
 * @deprecated in 2.17
 */
final class FinalStaticAccessFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Converts `static` access to `self` access in `final` classes.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Sample
{
    public function getFoo()
    {
        return static::class;
    }
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after FinalInternalClassFixer, PhpUnitTestCaseStaticMethodCallsFixer.
     */
    public function getPriority()
    {
        return parent::getPriority();
    }
    /**
     * {@inheritdoc}
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
        return [new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ClassNotation\SelfStaticAccessorFixer()];
    }
}
