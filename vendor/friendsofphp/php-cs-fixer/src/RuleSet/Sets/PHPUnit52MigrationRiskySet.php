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
final class PHPUnit52MigrationRiskySet extends \_PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\AbstractRuleSetDescription
{
    public function getRules()
    {
        return ['@PHPUnit50Migration:risky' => \true, 'php_unit_expectation' => ['target' => '5.2']];
    }
    public function getDescription()
    {
        return 'Rules to improve tests code for PHPUnit 5.2 compatibility.';
    }
}
