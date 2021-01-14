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

use _PhpScoper3fe455fa007d\PhpCsFixer\Diff\v3_0\Differ;
use _PhpScoper3fe455fa007d\PhpCsFixer\Diff\v3_0\Output\StrictUnifiedDiffOutputBuilder;
/**
 * @author SpacePossum
 */
final class UnifiedDiffer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Differ\DifferInterface
{
    /**
     * @var Differ
     */
    private $differ;
    public function __construct()
    {
        $this->differ = new \_PhpScoper3fe455fa007d\PhpCsFixer\Diff\v3_0\Differ(new \_PhpScoper3fe455fa007d\PhpCsFixer\Diff\v3_0\Output\StrictUnifiedDiffOutputBuilder(['fromFile' => 'Original', 'toFile' => 'New']));
    }
    /**
     * {@inheritdoc}
     */
    public function diff($old, $new)
    {
        return $this->differ->diff($old, $new);
    }
}
