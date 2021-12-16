<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Command;

use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Completion\CompletionInput;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Completion\CompletionSuggestions;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Exception\InvalidArgumentException;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Style\SymfonyStyle;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Data;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Command\Descriptor\DumpDescriptorInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Dumper\CliDumper;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Dumper\HtmlDumper;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Server\DumpServer;
/**
 * Starts a dump server to collect and output dumps on a single place with multiple formats support.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 *
 * @final
 */
class ServerDumpCommand extends \_PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'server:dump';
    protected static $defaultDescription = 'Start a dump server that collects and displays dumps in a single place';
    private $server;
    /** @var DumpDescriptorInterface[] */
    private $descriptors;
    public function __construct(\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Server\DumpServer $server, array $descriptors = [])
    {
        $this->server = $server;
        $this->descriptors = $descriptors + ['cli' => new \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor(new \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Dumper\CliDumper()), 'html' => new \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor(new \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Dumper\HtmlDumper())];
        parent::__construct();
    }
    protected function configure()
    {
        $this->addOption('format', null, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, \sprintf('The output format (%s)', \implode(', ', $this->getAvailableFormats())), 'cli')->setDescription(self::$defaultDescription)->setHelp(<<<'EOF'
<info>%command.name%</info> starts a dump server that collects and displays
dumps in a single place for debugging you application:

  <info>php %command.full_name%</info>

You can consult dumped data in HTML format in your browser by providing the <comment>--format=html</comment> option
and redirecting the output to a file:

  <info>php %command.full_name% --format="html" > dump.html</info>

EOF
);
    }
    protected function execute(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $io = new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Style\SymfonyStyle($input, $output);
        $format = $input->getOption('format');
        if (!($descriptor = $this->descriptors[$format] ?? null)) {
            throw new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('Unsupported format "%s".', $format));
        }
        $errorIo = $io->getErrorStyle();
        $errorIo->title('Symfony Var Dumper Server');
        $this->server->start();
        $errorIo->success(\sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');
        $this->server->listen(function (\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Data $data, array $context, int $clientId) use($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
        return 0;
    }
    public function complete(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Completion\CompletionInput $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Completion\CompletionSuggestions $suggestions) : void
    {
        if ($input->mustSuggestOptionValuesFor('format')) {
            $suggestions->suggestValues($this->getAvailableFormats());
        }
    }
    private function getAvailableFormats() : array
    {
        return \array_keys($this->descriptors);
    }
}
