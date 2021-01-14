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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\Sets;

use _PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\AbstractRuleSetDescription;
/**
 * @internal
 */
final class PHP70MigrationSet extends \_PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\AbstractRuleSetDescription
{
    public function getRules()
    {
        return ['@PHP56Migration' => \true, 'ternary_to_null_coalescing' => \true];
    }
    public function getDescription()
    {
        return 'Rules to improve code for PHP 7.0 compatibility.';
    }
}
