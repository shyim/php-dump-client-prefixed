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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Console\Command;

use _PhpScoper3fe455fa007d\PhpCsFixer\Config;
use _PhpScoper3fe455fa007d\PhpCsFixer\ConfigInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\ConfigurationResolver;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\ErrorOutput;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\NullOutput;
use _PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\ProcessOutput;
use _PhpScoper3fe455fa007d\PhpCsFixer\Error\ErrorsManager;
use _PhpScoper3fe455fa007d\PhpCsFixer\Report\ReportSummary;
use _PhpScoper3fe455fa007d\PhpCsFixer\Runner\Runner;
use _PhpScoper3fe455fa007d\PhpCsFixer\ToolInfoInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputArgument;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\ConsoleOutputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Terminal;
use _PhpScoper3fe455fa007d\Symfony\Component\EventDispatcher\EventDispatcher;
use _PhpScoper3fe455fa007d\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Stopwatch\Stopwatch;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FixCommand extends \_PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'fix';
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var ErrorsManager
     */
    private $errorsManager;
    /**
     * @var Stopwatch
     */
    private $stopwatch;
    /**
     * @var ConfigInterface
     */
    private $defaultConfig;
    /**
     * @var ToolInfoInterface
     */
    private $toolInfo;
    public function __construct(\_PhpScoper3fe455fa007d\PhpCsFixer\ToolInfoInterface $toolInfo)
    {
        parent::__construct();
        $this->defaultConfig = new \_PhpScoper3fe455fa007d\PhpCsFixer\Config();
        $this->errorsManager = new \_PhpScoper3fe455fa007d\PhpCsFixer\Error\ErrorsManager();
        $this->eventDispatcher = new \_PhpScoper3fe455fa007d\Symfony\Component\EventDispatcher\EventDispatcher();
        $this->stopwatch = new \_PhpScoper3fe455fa007d\Symfony\Component\Stopwatch\Stopwatch();
        $this->toolInfo = $toolInfo;
    }
    /**
     * {@inheritdoc}
     *
     * Override here to only generate the help copy when used.
     */
    public function getHelp()
    {
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::getHelpCopy();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputArgument('path', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'The path.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('path-mode', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Specify path mode (can be override or intersection).', 'override'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('allow-risky', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Are risky fixers allowed (can be yes or no).'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('config', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The path to a .php_cs file.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('dry-run', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Only shows which files would have been modified.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('rules', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The rules.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('using-cache', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Does cache should be used (can be yes or no).'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('cache-file', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The path to the cache file.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('diff', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Also produce diff for each file.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('diff-format', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Specify diff format.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('format', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'To output results in other formats.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('stop-on-violation', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Stop execution on first violation.'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('show-progress', '', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Type of progress indicator (none, run-in, estimating, estimating-max or dots).')])->setDescription('Fixes a directory or a file.');
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $verbosity = $output->getVerbosity();
        $passedConfig = $input->getOption('config');
        $passedRules = $input->getOption('rules');
        $resolver = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\ConfigurationResolver($this->defaultConfig, ['allow-risky' => $input->getOption('allow-risky'), 'config' => $passedConfig, 'dry-run' => $input->getOption('dry-run'), 'rules' => $passedRules, 'path' => $input->getArgument('path'), 'path-mode' => $input->getOption('path-mode'), 'using-cache' => $input->getOption('using-cache'), 'cache-file' => $input->getOption('cache-file'), 'format' => $input->getOption('format'), 'diff' => $input->getOption('diff'), 'diff-format' => $input->getOption('diff-format'), 'stop-on-violation' => $input->getOption('stop-on-violation'), 'verbosity' => $verbosity, 'show-progress' => $input->getOption('show-progress')], \getcwd(), $this->toolInfo);
        $reporter = $resolver->getReporter();
        $stdErr = $output instanceof \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\ConsoleOutputInterface ? $output->getErrorOutput() : ('txt' === $reporter->getFormat() ? $output : null);
        if (null !== $stdErr) {
            if (\_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $stdErr->writeln($this->getApplication()->getLongVersion());
                $stdErr->writeln(\sprintf('Runtime: <info>PHP %s</info>', \PHP_VERSION));
            }
            if (null !== $passedConfig && null !== $passedRules) {
                if (\getenv('PHP_CS_FIXER_FUTURE_MODE')) {
                    throw new \RuntimeException('Passing both `config` and `rules` options is not possible. This check was performed as `PHP_CS_FIXER_FUTURE_MODE` env var is set.');
                }
                $stdErr->writeln([\sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', 'When passing both "--config" and "--rules" the rules within the configuration file are not used.'), \sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', 'Passing both options is deprecated; version v3.0 PHP-CS-Fixer will exit with a configuration error code.')]);
            }
            $configFile = $resolver->getConfigFile();
            $stdErr->writeln(\sprintf('Loaded config <comment>%s</comment>%s.', $resolver->getConfig()->getName(), null === $configFile ? '' : ' from "' . $configFile . '"'));
            if ($resolver->getUsingCache()) {
                $cacheFile = $resolver->getCacheFile();
                if (\is_file($cacheFile)) {
                    $stdErr->writeln(\sprintf('Using cache file "%s".', $cacheFile));
                }
            }
        }
        $progressType = $resolver->getProgress();
        $finder = $resolver->getFinder();
        if (null !== $stdErr && $resolver->configFinderIsOverridden()) {
            $stdErr->writeln(\sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', 'Paths from configuration file have been overridden by paths provided as command arguments.'));
        }
        // @TODO 3.0 remove `run-in` and `estimating`
        if ('none' === $progressType || null === $stdErr) {
            $progressOutput = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\NullOutput();
        } elseif ('run-in' === $progressType) {
            $progressOutput = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\ProcessOutput($stdErr, $this->eventDispatcher, null, null);
        } else {
            $finder = new \ArrayIterator(\iterator_to_array($finder));
            $progressOutput = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\ProcessOutput($stdErr, $this->eventDispatcher, 'estimating' !== $progressType ? (new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Terminal())->getWidth() : null, \count($finder));
        }
        $runner = new \_PhpScoper3fe455fa007d\PhpCsFixer\Runner\Runner($finder, $resolver->getFixers(), $resolver->getDiffer(), 'none' !== $progressType ? $this->eventDispatcher : null, $this->errorsManager, $resolver->getLinter(), $resolver->isDryRun(), $resolver->getCacheManager(), $resolver->getDirectory(), $resolver->shouldStopOnViolation());
        $this->stopwatch->start('fixFiles');
        $changed = $runner->fix();
        $this->stopwatch->stop('fixFiles');
        $progressOutput->printLegend();
        $fixEvent = $this->stopwatch->getEvent('fixFiles');
        $reportSummary = new \_PhpScoper3fe455fa007d\PhpCsFixer\Report\ReportSummary($changed, $fixEvent->getDuration(), $fixEvent->getMemory(), \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE <= $verbosity, $resolver->isDryRun(), $output->isDecorated());
        $output->isDecorated() ? $output->write($reporter->generate($reportSummary)) : $output->write($reporter->generate($reportSummary), \false, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW);
        $invalidErrors = $this->errorsManager->getInvalidErrors();
        $exceptionErrors = $this->errorsManager->getExceptionErrors();
        $lintErrors = $this->errorsManager->getLintErrors();
        if (null !== $stdErr) {
            $errorOutput = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Output\ErrorOutput($stdErr);
            if (\count($invalidErrors) > 0) {
                $errorOutput->listErrors('linting before fixing', $invalidErrors);
            }
            if (\count($exceptionErrors) > 0) {
                $errorOutput->listErrors('fixing', $exceptionErrors);
            }
            if (\count($lintErrors) > 0) {
                $errorOutput->listErrors('linting after fixing', $lintErrors);
            }
        }
        $exitStatusCalculator = new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\FixCommandExitStatusCalculator();
        return $exitStatusCalculator->calculate($resolver->isDryRun(), \count($changed) > 0, \count($invalidErrors) > 0, \count($exceptionErrors) > 0, \count($lintErrors) > 0);
    }
}
