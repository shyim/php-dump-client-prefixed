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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\PhpUnit;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @deprecated since 2.16, replaced by PhpdocOrderByValueFixer
 *
 * @todo To be removed at 3.0
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpUnitOrderedCoversFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Order `@covers` annotation of PHPUnit tests.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @covers Foo
 * @covers Bar
 */
final class MyTest extends \\PHPUnit_Framework_TestCase
{}
')]);
    }
    public function getSuccessorsNames()
    {
        return \array_keys($this->proxyFixers);
    }
    protected function createProxyFixers()
    {
        $fixer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer();
        $fixer->configure(['annotations' => ['covers']]);
        return [$fixer];
    }
}
