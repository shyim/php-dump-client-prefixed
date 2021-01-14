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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration;

interface DeprecatedFixerOptionInterface extends \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionInterface
{
    /**
     * @return string
     */
    public function getDeprecationMessage();
}
