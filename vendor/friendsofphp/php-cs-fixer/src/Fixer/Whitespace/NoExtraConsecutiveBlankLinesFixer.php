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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Whitespace;

use _PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @deprecated in 2.10, proxy to NoExtraBlankLinesFixer
 */
final class NoExtraConsecutiveBlankLinesFixer extends \_PhpScoper3fe455fa007d\PhpCsFixer\AbstractProxyFixer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    private $fixer;
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return $this->getFixer()->getDefinition();
    }
    public function configure(array $configuration = null)
    {
        $this->getFixer()->configure($configuration);
        $this->configuration = $configuration;
    }
    public function getConfigurationDefinition()
    {
        return $this->getFixer()->getConfigurationDefinition();
    }
    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames()
    {
        return \array_keys($this->proxyFixers);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        return [$this->getFixer()];
    }
    private function getFixer()
    {
        if (null === $this->fixer) {
            $this->fixer = new \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer();
        }
        return $this->fixer;
    }
}
