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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Tests\Test;

use _PhpScoper3fe455fa007d\Symfony\Component\Finder\SplFileInfo;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
interface IntegrationCaseFactoryInterface
{
    /**
     * @return IntegrationCase
     */
    public function create(\_PhpScoper3fe455fa007d\Symfony\Component\Finder\SplFileInfo $file);
}