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
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class FinalClassFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('All classes must be final, except abstract ones and Doctrine entities.', [new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSample('<?php
class MyApp {}
')], 'No exception and no configuration are intentional. Beside Doctrine entities and of course abstract classes, there is no single reason not to declare all classes final. ' . 'If you want to subclass a class, mark the parent class as abstract and create two child classes, one empty if necessary: you\'ll gain much more fine grained type-hinting. ' . 'If you need to mock a standalone class, create an interface, or maybe it\'s a value-object that shouldn\'t be mocked at all. ' . 'If you need to extend a standalone class, create an interface and use the Composite pattern. ' . 'If you aren\'t ready yet for serious OOP, go with FinalInternalClassFixer, it\'s fine.', 'Risky when subclassing non-abstract classes.');
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        $fixer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer();
        $fixer->configure(['annotation_include' => [], 'consider_absent_docblock_as_internal_class' => \true]);
        return [$fixer];
    }
}
