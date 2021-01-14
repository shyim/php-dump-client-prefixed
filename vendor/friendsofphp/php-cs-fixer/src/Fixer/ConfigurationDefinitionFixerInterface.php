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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer;

use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
/**
 * @deprecated Will be incorporated into `ConfigurableFixerInterface` in 3.0
 */
interface ConfigurationDefinitionFixerInterface extends \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * Defines the available configuration options of the fixer.
     *
     * @return FixerConfigurationResolverInterface
     */
    public function getConfigurationDefinition();
}
