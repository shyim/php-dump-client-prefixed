<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper3fe455fa007d\Symfony\Component\EventDispatcher\DependencyInjection;

use _PhpScoper3fe455fa007d\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * This pass allows bundles to extend the list of event aliases.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 */
class AddEventAliasesPass implements \_PhpScoper3fe455fa007d\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $eventAliases;
    private $eventAliasesParameter;
    public function __construct(array $eventAliases, string $eventAliasesParameter = 'event_dispatcher.event_aliases')
    {
        $this->eventAliases = $eventAliases;
        $this->eventAliasesParameter = $eventAliasesParameter;
    }
    public function process(\_PhpScoper3fe455fa007d\Symfony\Component\DependencyInjection\ContainerBuilder $container) : void
    {
        $eventAliases = $container->hasParameter($this->eventAliasesParameter) ? $container->getParameter($this->eventAliasesParameter) : [];
        $container->setParameter($this->eventAliasesParameter, \array_merge($eventAliases, $this->eventAliases));
    }
}
