<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Exception;
use _PhpScoper3fe455fa007d\PackageVersions\Versions;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Application;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command;
/**
 * Handles running the Console Tools inside Symfony Console context.
 */
class ConsoleRunner
{
    /**
     * Runs console with the given connection provider.
     *
     * @param Command[] $commands
     *
     * @return void
     *
     * @throws Exception
     */
    public static function run(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider $connectionProvider, $commands = [])
    {
        $cli = new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Application('Doctrine Command Line Interface', \_PhpScoper3fe455fa007d\PackageVersions\Versions::getVersion(\_PhpScoper3fe455fa007d\PackageVersions\Versions::rootPackageName()));
        $cli->setCatchExceptions(\true);
        self::addCommands($cli, $connectionProvider);
        $cli->addCommands($commands);
        $cli->run();
    }
    /**
     * @return void
     */
    public static function addCommands(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Application $cli, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider $connectionProvider)
    {
        $cli->addCommands([new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand($connectionProvider), new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand($connectionProvider)]);
    }
    /**
     * Prints the instructions to create a configuration file
     *
     * @return void
     */
    public static function printCliConfigTemplate()
    {
        echo <<<'HELP'
You are missing a "cli-config.php" or "config/cli-config.php" file in your
project, which is required to get the Doctrine-DBAL Console working. You can use the
following sample as a template:

<?php
use Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider;

// You can append new commands to $commands array, if needed

// replace with the mechanism to retrieve DBAL connection(s) in your app
// and return a Doctrine\DBAL\Tools\Console\ConnectionProvider instance.
$connection = getDBALConnection();

// in case you have a single connection you can use SingleConnectionProvider
// otherwise you need to implement the Doctrine\DBAL\Tools\Console\ConnectionProvider interface with your custom logic
return new SingleConnectionProvider($connection);

HELP;
    }
}
