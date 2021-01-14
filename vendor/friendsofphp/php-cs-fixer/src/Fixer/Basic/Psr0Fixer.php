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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Basic;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Bram Gotink <bram@gotink.me>
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @deprecated
 */
final class Psr0Fixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface
{
    /**
     * @var PsrAutoloadingFixer
     */
    private $fixer;
    public function __construct()
    {
        $this->fixer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer();
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $definition = $this->fixer->getDefinition();
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.', $definition->getCodeSamples(), $definition->getDescription(), $definition->getRiskyDescription());
    }
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->fixer->configure($configuration);
    }
    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames()
    {
        return [$this->fixer->getName()];
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('dir', 'The directory where the project code is placed.'))->setAllowedTypes(['string'])->setDefault('')->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        return [$this->fixer];
    }
}
