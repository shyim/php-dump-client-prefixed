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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Linter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface LintingResultInterface
{
    /**
     * Check if linting process was successful and raise LintingException if not.
     */
    public function check();
}
