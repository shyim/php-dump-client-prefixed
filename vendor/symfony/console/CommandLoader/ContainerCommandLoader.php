<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper3fe455fa007d\Symfony\Component\Console\CommandLoader;

use _PhpScoper3fe455fa007d\Psr\Container\ContainerInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Exception\CommandNotFoundException;
/**
 * Loads commands from a PSR-11 container.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class ContainerCommandLoader implements \_PhpScoper3fe455fa007d\Symfony\Component\Console\CommandLoader\CommandLoaderInterface
{
    private $container;
    private $commandMap;
    /**
     * @param array $commandMap An array with command names as keys and service ids as values
     */
    public function __construct(\_PhpScoper3fe455fa007d\Psr\Container\ContainerInterface $container, array $commandMap)
    {
        $this->container = $container;
        $this->commandMap = $commandMap;
    }
    /**
     * {@inheritdoc}
     */
    public function get(string $name)
    {
        if (!$this->has($name)) {
            throw new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Exception\CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
        }
        return $this->container->get($this->commandMap[$name]);
    }
    /**
     * {@inheritdoc}
     */
    public function has(string $name)
    {
        return isset($this->commandMap[$name]) && $this->container->has($this->commandMap[$name]);
    }
    /**
     * {@inheritdoc}
     */
    public function getNames()
    {
        return \array_keys($this->commandMap);
    }
}
