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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Runner;

use _PhpScoper3fe455fa007d\PhpCsFixer\Cache\CacheManagerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Event\Event;
use _PhpScoper3fe455fa007d\PhpCsFixer\FileReader;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerFileProcessedEvent;
use _PhpScoper3fe455fa007d\Symfony\Component\EventDispatcher\EventDispatcherInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileFilterIterator extends \FilterIterator
{
    /**
     * @var null|EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;
    /**
     * @var array<string,bool>
     */
    private $visitedElements = [];
    public function __construct(\Traversable $iterator, \_PhpScoper3fe455fa007d\Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher = null, \_PhpScoper3fe455fa007d\PhpCsFixer\Cache\CacheManagerInterface $cacheManager)
    {
        if (!$iterator instanceof \Iterator) {
            $iterator = new \IteratorIterator($iterator);
        }
        parent::__construct($iterator);
        $this->eventDispatcher = $eventDispatcher;
        $this->cacheManager = $cacheManager;
    }
    public function accept()
    {
        $file = $this->current();
        if (!$file instanceof \SplFileInfo) {
            throw new \RuntimeException(\sprintf('Expected instance of "\\SplFileInfo", got "%s".', \is_object($file) ? \get_class($file) : \gettype($file)));
        }
        $path = $file->isLink() ? $file->getPathname() : $file->getRealPath();
        if (isset($this->visitedElements[$path])) {
            return \false;
        }
        $this->visitedElements[$path] = \true;
        if (!$file->isFile() || $file->isLink()) {
            return \false;
        }
        $content = \_PhpScoper3fe455fa007d\PhpCsFixer\FileReader::createSingleton()->read($path);
        // mark as skipped:
        if ('' === $content || !$this->cacheManager->needFixing($file->getPathname(), $content)) {
            $this->dispatchEvent(\_PhpScoper3fe455fa007d\PhpCsFixer\FixerFileProcessedEvent::NAME, new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerFileProcessedEvent(\_PhpScoper3fe455fa007d\PhpCsFixer\FixerFileProcessedEvent::STATUS_SKIPPED));
            return \false;
        }
        return \true;
    }
    /**
     * @param string $name
     */
    private function dispatchEvent($name, \_PhpScoper3fe455fa007d\PhpCsFixer\Event\Event $event)
    {
        if (null === $this->eventDispatcher) {
            return;
        }
        // BC compatibility < Sf 4.3
        if (!$this->eventDispatcher instanceof \_PhpScoper3fe455fa007d\Symfony\Contracts\EventDispatcher\EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($name, $event);
            return;
        }
        $this->eventDispatcher->dispatch($event, $name);
    }
}
