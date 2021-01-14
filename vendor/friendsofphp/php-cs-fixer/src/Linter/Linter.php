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

/**
 * Handle PHP code linting process.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Linter implements \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\LinterInterface
{
    /**
     * @var LinterInterface
     */
    private $sublinter;
    /**
     * @param null|string $executable PHP executable, null for autodetection
     */
    public function __construct($executable = null)
    {
        try {
            $this->sublinter = new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\TokenizerLinter();
        } catch (\_PhpScoper3fe455fa007d\PhpCsFixer\Linter\UnavailableLinterException $e) {
            $this->sublinter = new \_PhpScoper3fe455fa007d\PhpCsFixer\Linter\ProcessLinter($executable);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isAsync()
    {
        return $this->sublinter->isAsync();
    }
    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return $this->sublinter->lintFile($path);
    }
    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        return $this->sublinter->lintSource($source);
    }
}
