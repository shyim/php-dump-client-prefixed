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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Linter;

use _PhpScoper3fe455fa007d\PhpCsFixer\FileReader;
use _PhpScoper3fe455fa007d\PhpCsFixer\FileRemoval;
use _PhpScoper3fe455fa007d\Symfony\Component\Filesystem\Exception\IOException;
use _PhpScoper3fe455fa007d\Symfony\Component\Process\PhpExecutableFinder;
use _PhpScoper3fe455fa007d\Symfony\Component\Process\Process;
/**
 * Handle PHP code linting using separated process of `php -l _file_`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ProcessLinter implements \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\LinterInterface
{
    /**
     * @var FileRemoval
     */
    private $fileRemoval;
    /**
     * @var ProcessLinterProcessBuilder
     */
    private $processBuilder;
    /**
     * Temporary file for code linting.
     *
     * @var null|string
     */
    private $temporaryFile;
    /**
     * @param null|string $executable PHP executable, null for autodetection
     */
    public function __construct($executable = null)
    {
        if (null === $executable) {
            $executableFinder = new \_PhpScoper3fe455fa007d\Symfony\Component\Process\PhpExecutableFinder();
            $executable = $executableFinder->find(\false);
            if (\false === $executable) {
                throw new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\UnavailableLinterException('Cannot find PHP executable.');
            }
            if ('phpdbg' === \PHP_SAPI) {
                if (\false === \strpos($executable, 'phpdbg')) {
                    throw new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\UnavailableLinterException('Automatically found PHP executable is non-standard phpdbg. Could not find proper PHP executable.');
                }
                // automatically found executable is `phpdbg`, let us try to fallback to regular `php`
                $executable = \str_replace('phpdbg', 'php', $executable);
                if (!\is_executable($executable)) {
                    throw new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\UnavailableLinterException('Automatically found PHP executable is phpdbg. Could not find proper PHP executable.');
                }
            }
        }
        $this->processBuilder = new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\ProcessLinterProcessBuilder($executable);
        $this->fileRemoval = new \_PhpScoper3fe455fa007d\PhpCsFixer\FileRemoval();
    }
    public function __destruct()
    {
        if (null !== $this->temporaryFile) {
            $this->fileRemoval->delete($this->temporaryFile);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isAsync()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\ProcessLintingResult($this->createProcessForFile($path), $path);
    }
    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        return new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\ProcessLintingResult($this->createProcessForSource($source), $this->temporaryFile);
    }
    /**
     * @param string $path path to file
     *
     * @return Process
     */
    private function createProcessForFile($path)
    {
        // in case php://stdin
        if (!\is_file($path)) {
            return $this->createProcessForSource(\_PhpScoper3fe455fa007d\PhpCsFixer\FileReader::createSingleton()->read($path));
        }
        $process = $this->processBuilder->build($path);
        $process->setTimeout(10);
        $process->start();
        return $process;
    }
    /**
     * Create process that lint PHP code.
     *
     * @param string $source code
     *
     * @return Process
     */
    private function createProcessForSource($source)
    {
        if (null === $this->temporaryFile) {
            $this->temporaryFile = \tempnam(\sys_get_temp_dir(), 'cs_fixer_tmp_');
            $this->fileRemoval->observe($this->temporaryFile);
        }
        if (\false === @\file_put_contents($this->temporaryFile, $source)) {
            throw new \_PhpScoper3fe455fa007d\Symfony\Component\Filesystem\Exception\IOException(\sprintf('Failed to write file "%s".', $this->temporaryFile), 0, null, $this->temporaryFile);
        }
        return $this->createProcessForFile($this->temporaryFile);
    }
}
