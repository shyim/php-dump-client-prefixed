<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\Command;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\DB2Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\KeywordList;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MariaDb102Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQL80Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQLKeywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\OracleKeywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\PostgreSQL100Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\PostgreSQL94Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\ReservedKeywordsValidator;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\SQLiteKeywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\SQLServer2012Keywords;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider;
use InvalidArgumentException;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption;
use _PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface;
use function array_keys;
use function assert;
use function count;
use function implode;
use function is_array;
use function is_string;
class ReservedWordsCommand extends \_PhpScoper3fe455fa007d\Symfony\Component\Console\Command\Command
{
    /** @var array<string,class-string<KeywordList>> */
    private $keywordListClasses = ['db2' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\DB2Keywords::class, 'mysql' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQLKeywords::class, 'mysql57' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords::class, 'mysql80' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MySQL80Keywords::class, 'mariadb102' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\MariaDb102Keywords::class, 'oracle' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\OracleKeywords::class, 'pgsql' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\PostgreSQL94Keywords::class, 'pgsql100' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\PostgreSQL100Keywords::class, 'sqlite' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\SQLiteKeywords::class, 'sqlserver' => \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\SQLServer2012Keywords::class];
    /** @var ConnectionProvider */
    private $connectionProvider;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider $connectionProvider)
    {
        parent::__construct();
        $this->connectionProvider = $connectionProvider;
    }
    /**
     * If you want to add or replace a keywords list use this command.
     *
     * @param string                    $name
     * @param class-string<KeywordList> $class
     *
     * @return void
     */
    public function setKeywordListClass($name, $class)
    {
        $this->keywordListClasses[$name] = $class;
    }
    /** @return void */
    protected function configure()
    {
        $this->setName('dbal:reserved-words')->setDescription('Checks if the current database contains identifiers that are reserved.')->setDefinition([new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('connection', null, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The named database connection'), new \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption('list', 'l', \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL | \_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputOption::VALUE_IS_ARRAY, 'Keyword-List name.')])->setHelp(<<<EOT
Checks if the current database contains tables and columns
with names that are identifiers in this dialect or in other SQL dialects.

By default SQLite, MySQL, PostgreSQL, Microsoft SQL Server and Oracle
keywords are checked:

    <info>%command.full_name%</info>

If you want to check against specific dialects you can
pass them to the command:

    <info>%command.full_name% -l mysql -l pgsql</info>

The following keyword lists are currently shipped with Doctrine:

    * mysql
    * mysql57
    * mysql80
    * mariadb102
    * pgsql
    * pgsql100
    * sqlite
    * oracle
    * sqlserver
    * sqlserver2012
    * db2 (Not checked by default)
EOT
);
    }
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function execute(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input, \_PhpScoper3fe455fa007d\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $conn = $this->getConnection($input);
        $keywordLists = $input->getOption('list');
        if (\is_string($keywordLists)) {
            $keywordLists = [$keywordLists];
        } elseif (!\is_array($keywordLists)) {
            $keywordLists = [];
        }
        if (\count($keywordLists) === 0) {
            $keywordLists = \array_keys($this->keywordListClasses);
        }
        $keywords = [];
        foreach ($keywordLists as $keywordList) {
            if (!isset($this->keywordListClasses[$keywordList])) {
                throw new \InvalidArgumentException("There exists no keyword list with name '" . $keywordList . "'. " . 'Known lists: ' . \implode(', ', \array_keys($this->keywordListClasses)));
            }
            $class = $this->keywordListClasses[$keywordList];
            $keywords[] = new $class();
        }
        $output->write('Checking keyword violations for <comment>' . \implode(', ', $keywordLists) . '</comment>...', \true);
        $schema = $conn->getSchemaManager()->createSchema();
        $visitor = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\Keywords\ReservedKeywordsValidator($keywords);
        $schema->visit($visitor);
        $violations = $visitor->getViolations();
        if (\count($violations) !== 0) {
            $output->write('There are <error>' . \count($violations) . '</error> reserved keyword violations' . ' in your database schema:', \true);
            foreach ($violations as $violation) {
                $output->write('  - ' . $violation, \true);
            }
            return 1;
        }
        $output->write('No reserved keywords violations have been found!', \true);
        return 0;
    }
    private function getConnection(\_PhpScoper3fe455fa007d\Symfony\Component\Console\Input\InputInterface $input) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection
    {
        $connectionName = $input->getOption('connection');
        \assert(\is_string($connectionName) || $connectionName === null);
        if ($connectionName !== null) {
            return $this->connectionProvider->getConnection($connectionName);
        }
        return $this->connectionProvider->getDefaultConnection();
    }
}
