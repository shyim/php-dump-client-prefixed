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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Differ;

use _PhpScoper3fe455fa007d\PhpCsFixer\Diff\v1_4\Differ;
/**
 * @author SpacePossum
 */
final class SebastianBergmannShortDiffer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Differ\DifferInterface
{
    /**
     * @var Differ
     */
    private $differ;
    public function __construct()
    {
        $this->differ = new \_PhpScoper3fe455fa007d\PhpCsFixer\Diff\v1_4\Differ("--- Original\n+++ New\n", \false);
    }
    /**
     * {@inheritdoc}
     */
    public function diff($old, $new)
    {
        return $this->differ->diff($old, $new);
    }
}
