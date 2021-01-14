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

use _PhpScoper3fe455fa007d\PhpCsFixer\Differ\DiffConsoleFormatter;
use _PhpScoper3fe455fa007d\PhpCsFixer\Differ\FullDiffer;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurableFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\Fixer\FixerInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSampleInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use _PhpScoper3fe455fa007d\PhpCsFixer\FixerFactory;
use _PhpScoper3fe455fa007d\PhpCsFixer\Preg;
use _PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\RuleSets;
use _PhpScoper3fe455fa007d\PhpCsFixer\StdinFileInfo;
use _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens;
use _PhpScoper3fe455fa007d\PhpCsFixer\Utils;
use _PhpScoper3fe455fa007d\PhpCsFixer\WordMatcher;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Formatter\OutputFormatter;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputArgument;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\ConsoleOutputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class DescribeCommand extends \_PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'describe';
    /**
     * @var string[]
     */
    private $setNames;
    /**
     * @var FixerFactory
     */
    private $fixerFactory;
    /**
     * @var array<string, FixerInterface>
     */
    private $fixers;
    public function __construct(\_PhpScoper3fe455fa007d\PhpCsFixer\FixerFactory $fixerFactory = null)
    {
        parent::__construct();
        if (null === $fixerFactory) {
            $fixerFactory = new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerFactory();
            $fixerFactory->registerBuiltInFixers();
        }
        $this->fixerFactory = $fixerFactory;
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputArgument('name', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Name of rule / set.')])->setDescription('Describe rule / ruleset.');
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output)
    {
        if (\_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity() && $output instanceof \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\ConsoleOutputInterface) {
            $stdErr = $output->getErrorOutput();
            $stdErr->writeln($this->getApplication()->getLongVersion());
            $stdErr->writeln(\sprintf('Runtime: <info>PHP %s</info>', \PHP_VERSION));
        }
        $name = $input->getArgument('name');
        try {
            if ('@' === $name[0]) {
                $this->describeSet($output, $name);
                return 0;
            }
            $this->describeRule($output, $name);
        } catch (\_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\DescribeNameNotFoundException $e) {
            $matcher = new \_PhpScoper3fe455fa007d\PhpCsFixer\WordMatcher('set' === $e->getType() ? $this->getSetNames() : \array_keys($this->getFixers()));
            $alternative = $matcher->match($name);
            $this->describeList($output, $e->getType());
            throw new \InvalidArgumentException(\sprintf('%s "%s" not found.%s', \ucfirst($e->getType()), $name, null === $alternative ? '' : ' Did you mean "' . $alternative . '"?'));
        }
        return 0;
    }
    /**
     * @param string $name
     */
    private function describeRule(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output, $name)
    {
        $fixers = $this->getFixers();
        if (!isset($fixers[$name])) {
            throw new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\DescribeNameNotFoundException($name, 'rule');
        }
        /** @var FixerInterface $fixer */
        $fixer = $fixers[$name];
        if ($fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface) {
            $definition = $fixer->getDefinition();
        } else {
            $definition = new \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FixerDefinition('Description is not available.', []);
        }
        $description = $definition->getSummary();
        if ($fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DeprecatedFixerInterface) {
            $successors = $fixer->getSuccessorsNames();
            $message = [] === $successors ? 'will be removed on next major version' : \sprintf('use %s instead', \_PhpScoper3fe455fa007d\PhpCsFixer\Utils::naturalLanguageJoinWithBackticks($successors));
            $message = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(`.+?`)/', '<info>$1</info>', $message);
            $description .= \sprintf(' <error>DEPRECATED</error>: %s.', $message);
        }
        $output->writeln(\sprintf('<info>Description of</info> %s <info>rule</info>.', $name));
        if ($output->getVerbosity() >= \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(\sprintf('Fixer class: <comment>%s</comment>.', \get_class($fixer)));
        }
        $output->writeln($description);
        if ($definition->getDescription()) {
            $output->writeln($definition->getDescription());
        }
        $output->writeln('');
        if ($fixer->isRisky()) {
            $output->writeln('<error>Fixer applying this rule is risky.</error>');
            if ($definition->getRiskyDescription()) {
                $output->writeln($definition->getRiskyDescription());
            }
            $output->writeln('');
        }
        if ($fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface) {
            $configurationDefinition = $fixer->getConfigurationDefinition();
            $options = $configurationDefinition->getOptions();
            $output->writeln(\sprintf('Fixer is configurable using following option%s:', 1 === \count($options) ? '' : 's'));
            foreach ($options as $option) {
                $line = '* <info>' . \_PhpScoper3fe455fa007d\Symfony\Component\Console\Formatter\OutputFormatter::escape($option->getName()) . '</info>';
                $allowed = \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::getDisplayableAllowedValues($option);
                if (null !== $allowed) {
                    foreach ($allowed as &$value) {
                        if ($value instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AllowedValueSubset) {
                            $value = 'a subset of <comment>' . \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::toString($value->getAllowedValues()) . '</comment>';
                        } else {
                            $value = '<comment>' . \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::toString($value) . '</comment>';
                        }
                    }
                } else {
                    $allowed = \array_map(static function ($type) {
                        return '<comment>' . $type . '</comment>';
                    }, $option->getAllowedTypes());
                }
                if (null !== $allowed) {
                    $line .= ' (' . \implode(', ', $allowed) . ')';
                }
                $description = \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(`.+?`)/', '<info>$1</info>', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Formatter\OutputFormatter::escape($option->getDescription()));
                $line .= ': ' . \lcfirst(\_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/\\.$/', '', $description)) . '; ';
                if ($option->hasDefault()) {
                    $line .= \sprintf('defaults to <comment>%s</comment>', \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::toString($option->getDefault()));
                } else {
                    $line .= '<comment>required</comment>';
                }
                if ($option instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\DeprecatedFixerOption) {
                    $line .= '. <error>DEPRECATED</error>: ' . \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replace('/(`.+?`)/', '<info>$1</info>', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Formatter\OutputFormatter::escape(\lcfirst($option->getDeprecationMessage())));
                }
                if ($option instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerConfiguration\AliasedFixerOption) {
                    $line .= '; <error>DEPRECATED</error> alias: <comment>' . $option->getAlias() . '</comment>';
                }
                $output->writeln($line);
            }
            $output->writeln('');
        } elseif ($fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            $output->writeln('<comment>Fixer is configurable.</comment>');
            if ($definition->getConfigurationDescription()) {
                $output->writeln($definition->getConfigurationDescription());
            }
            if ($definition->getDefaultConfiguration()) {
                $output->writeln(\sprintf('Default configuration: <comment>%s</comment>.', \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::toString($definition->getDefaultConfiguration())));
            }
            $output->writeln('');
        }
        /** @var CodeSampleInterface[] $codeSamples */
        $codeSamples = \array_filter($definition->getCodeSamples(), static function (\_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\CodeSampleInterface $codeSample) {
            if ($codeSample instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface) {
                return $codeSample->isSuitableFor(\PHP_VERSION_ID);
            }
            return \true;
        });
        if (!\count($codeSamples)) {
            $output->writeln(['Fixing examples can not be demonstrated on the current PHP version.', '']);
        } else {
            $output->writeln('Fixing examples:');
            $differ = new \_PhpScoper3fe455fa007d\PhpCsFixer\Differ\FullDiffer();
            $diffFormatter = new \_PhpScoper3fe455fa007d\PhpCsFixer\Differ\DiffConsoleFormatter($output->isDecorated(), \sprintf('<comment>   ---------- begin diff ----------</comment>%s%%s%s<comment>   ----------- end diff -----------</comment>', \PHP_EOL, \PHP_EOL));
            foreach ($codeSamples as $index => $codeSample) {
                $old = $codeSample->getCode();
                $tokens = \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\Tokens::fromCode($old);
                $configuration = $codeSample->getConfiguration();
                if ($fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurableFixerInterface) {
                    $fixer->configure(null === $configuration ? [] : $configuration);
                }
                $file = $codeSample instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface ? $codeSample->getSplFileInfo() : new \_PhpScoper3fe455fa007d\PhpCsFixer\StdinFileInfo();
                $fixer->fix($file, $tokens);
                $diff = $differ->diff($old, $tokens->generateCode());
                if ($fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\ConfigurableFixerInterface) {
                    if (null === $configuration) {
                        $output->writeln(\sprintf(' * Example #%d. Fixing with the <comment>default</comment> configuration.', $index + 1));
                    } else {
                        $output->writeln(\sprintf(' * Example #%d. Fixing with configuration: <comment>%s</comment>.', $index + 1, \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::toString($codeSample->getConfiguration())));
                    }
                } else {
                    $output->writeln(\sprintf(' * Example #%d.', $index + 1));
                }
                $output->writeln([$diffFormatter->format($diff, '   %s'), '']);
            }
        }
    }
    /**
     * @param string $name
     */
    private function describeSet(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output, $name)
    {
        if (!\in_array($name, $this->getSetNames(), \true)) {
            throw new \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\DescribeNameNotFoundException($name, 'set');
        }
        $ruleSetDefinitions = \_PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\RuleSets::getSetDefinitions();
        $fixers = $this->getFixers();
        $output->writeln(\sprintf('<info>Description of the</info> %s <info>set.</info>', $ruleSetDefinitions[$name]->getName()));
        $output->writeln($this->replaceRstLinks($ruleSetDefinitions[$name]->getDescription()));
        if ($ruleSetDefinitions[$name]->isRisky()) {
            $output->writeln('This set contains <error>risky</error> rules.');
        }
        $output->writeln('');
        $help = '';
        foreach ($ruleSetDefinitions[$name]->getRules() as $rule => $config) {
            if ('@' === $rule[0]) {
                $set = $ruleSetDefinitions[$rule];
                $help .= \sprintf(" * <info>%s</info>%s\n   | %s\n\n", $rule, $set->isRisky() ? ' <error>risky</error>' : '', $this->replaceRstLinks($set->getDescription()));
                continue;
            }
            $fixer = $fixers[$rule];
            if (!$fixer instanceof \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface) {
                throw new \RuntimeException(\sprintf('Cannot describe rule %s, the fixer does not implement "%s".', $rule, \_PhpScoper3fe455fa007d\PhpCsFixer\Fixer\DefinedFixerInterface::class));
            }
            $definition = $fixer->getDefinition();
            $help .= \sprintf(" * <info>%s</info>%s\n   | %s\n%s\n", $rule, $fixer->isRisky() ? ' <error>risky</error>' : '', $definition->getSummary(), \true !== $config ? \sprintf("   <comment>| Configuration: %s</comment>\n", \_PhpScoper3fe455fa007d\PhpCsFixer\Console\Command\HelpCommand::toString($config)) : '');
        }
        $output->write($help);
    }
    /**
     * @return array<string, FixerInterface>
     */
    private function getFixers()
    {
        if (null !== $this->fixers) {
            return $this->fixers;
        }
        $fixers = [];
        foreach ($this->fixerFactory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }
        $this->fixers = $fixers;
        \ksort($this->fixers);
        return $this->fixers;
    }
    /**
     * @return string[]
     */
    private function getSetNames()
    {
        if (null !== $this->setNames) {
            return $this->setNames;
        }
        $this->setNames = \_PhpScoper3fe455fa007d\PhpCsFixer\RuleSet\RuleSets::getSetDefinitionNames();
        return $this->setNames;
    }
    /**
     * @param string $type 'rule'|'set'
     */
    private function describeList(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output, $type)
    {
        if ($output->getVerbosity() >= \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $describe = ['sets' => $this->getSetNames(), 'rules' => $this->getFixers()];
        } elseif ($output->getVerbosity() >= \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE) {
            $describe = 'set' === $type ? ['sets' => $this->getSetNames()] : ['rules' => $this->getFixers()];
        } else {
            return;
        }
        /** @var string[] $items */
        foreach ($describe as $list => $items) {
            $output->writeln(\sprintf('<comment>Defined %s:</comment>', $list));
            foreach ($items as $name => $item) {
                $output->writeln(\sprintf('* <info>%s</info>', \is_string($name) ? $name : $item));
            }
        }
    }
    /**
     * @param string $content
     *
     * @return string
     */
    private function replaceRstLinks($content)
    {
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replaceCallback('/(`[^<]+<[^>]+>`_)/', static function (array $matches) {
            return \_PhpScoper3fe455fa007d\PhpCsFixer\Preg::replaceCallback('/`(.*)<(.*)>`_/', static function (array $matches) {
                return $matches[1] . '(' . $matches[2] . ')';
            }, $matches[1]);
        }, $content);
    }
}
