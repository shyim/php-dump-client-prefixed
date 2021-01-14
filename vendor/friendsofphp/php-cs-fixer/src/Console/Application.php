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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Console;

use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\DescribeCommand;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\FixCommand;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\SelfUpdateCommand;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate\GithubClient;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use _PhpScoper3fe455fa007d\PhpCsFixer\PharChecker;
use _PhpScoper3fe455fa007d\PhpCsFixer\ToolInfo;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Application as BaseApplication;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\ListCommand;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\ConsoleOutputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Application extends \_PhpScoper3fe455fa007d\Symfony\Component\Console\Application
{
    const VERSION = '2.17.3';
    const VERSION_CODENAME = 'Desert Beast';
    /**
     * @var ToolInfo
     */
    private $toolInfo;
    public function __construct()
    {
        if (!\getenv('PHP_CS_FIXER_FUTURE_MODE')) {
            \error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);
        }
        parent::__construct('PHP CS Fixer', self::VERSION);
        $this->toolInfo = new \_PhpScoper3fe455fa007d\PhpCsFixer\ToolInfo();
        $this->add(new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\DescribeCommand());
        $this->add(new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\FixCommand($this->toolInfo));
        $this->add(new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\SelfUpdateCommand(new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate\NewVersionChecker(new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate\GithubClient()), $this->toolInfo, new \_PhpScoper3fe455fa007d\PhpCsFixer\PharChecker()));
    }
    /**
     * @return int
     */
    public static function getMajorVersion()
    {
        return (int) \explode('.', self::VERSION)[0];
    }
    /**
     * {@inheritdoc}
     */
    public function doRun(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $stdErr = $output instanceof \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\ConsoleOutputInterface ? $output->getErrorOutput() : ($input->hasParameterOption('--format', \true) && 'txt' !== $input->getParameterOption('--format', null, \true) ? null : $output);
        if (null !== $stdErr) {
            $warningsDetector = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\WarningsDetector($this->toolInfo);
            $warningsDetector->detectOldVendor();
            $warningsDetector->detectOldMajor();
            foreach ($warningsDetector->getWarnings() as $warning) {
                $stdErr->writeln(\sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', $warning));
            }
        }
        return parent::doRun($input, $output);
    }
    /**
     * {@inheritdoc}
     */
    public function getLongVersion()
    {
        $version = \sprintf('%s <info>%s</info> by <comment>Fabien Potencier</comment> and <comment>Dariusz Ruminski</comment>', parent::getLongVersion(), self::VERSION_CODENAME);
        $commit = '@git-commit@';
        if ('@' . 'git-commit@' !== $commit) {
            $version .= ' (' . \substr($commit, 0, 7) . ')';
        }
        return $version;
    }
    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return [new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand(), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Command\ListCommand()];
    }
}
