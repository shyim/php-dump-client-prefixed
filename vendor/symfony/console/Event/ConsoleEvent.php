<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper3fe455fa007d\Symfony\Component\Console\Event;

use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface;
use _PhpScoper3fe455fa007d\Symfony\Contracts\EventDispatcher\Event;
/**
 * Allows to inspect input and output of a command.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
class ConsoleEvent extends \_PhpScoper3fe455fa007d\Symfony\Contracts\EventDispatcher\Event
{
    protected $command;
    private $input;
    private $output;
    public function __construct(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command $command = null, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }
    /**
     * Gets the command that is executed.
     *
     * @return Command|null A Command instance
     */
    public function getCommand()
    {
        return $this->command;
    }
    /**
     * Gets the input instance.
     *
     * @return InputInterface An InputInterface instance
     */
    public function getInput()
    {
        return $this->input;
    }
    /**
     * Gets the output instance.
     *
     * @return OutputInterface An OutputInterface instance
     */
    public function getOutput()
    {
        return $this->output;
    }
}
