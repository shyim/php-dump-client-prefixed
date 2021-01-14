<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Id;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\DriverManager;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\LockMode;
use Throwable;
use function array_change_key_case;
use function assert;
use function is_int;
use const CASE_LOWER;
/**
 * Table ID Generator for those poor languages that are missing sequences.
 *
 * WARNING: The Table Id Generator clones a second independent database
 * connection to work correctly. This means using the generator requests that
 * generate IDs will have two open database connections. This is necessary to
 * be safe from transaction failures in the main connection. Make sure to only
 * ever use one TableGenerator otherwise you end up with many connections.
 *
 * TableID Generator does not work with SQLite.
 *
 * The TableGenerator does not take care of creating the SQL Table itself. You
 * should look at the `TableGeneratorSchemaVisitor` to do this for you.
 * Otherwise the schema for a table looks like:
 *
 * CREATE sequences (
 *   sequence_name VARCHAR(255) NOT NULL,
 *   sequence_value INT NOT NULL DEFAULT 1,
 *   sequence_increment_by INT NOT NULL DEFAULT 1,
 *   PRIMARY KEY (sequence_name)
 * );
 *
 * Technically this generator works as follows:
 *
 * 1. Use a robust transaction serialization level.
 * 2. Open transaction
 * 3. Acquire a read lock on the table row (SELECT .. FOR UPDATE)
 * 4. Increment current value by one and write back to database
 * 5. Commit transaction
 *
 * If you are using a sequence_increment_by value that is larger than one the
 * ID Generator will keep incrementing values until it hits the incrementation
 * gap before issuing another query.
 *
 * If no row is present for a given sequence a new one will be created with the
 * default values 'value' = 1 and 'increment_by' = 1
 */
class TableGenerator
{
    /** @var Connection */
    private $conn;
    /** @var string */
    private $generatorTableName;
    /** @var mixed[][] */
    private $sequences = [];
    /**
     * @param string $generatorTableName
     *
     * @throws Exception
     */
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, $generatorTableName = 'sequences')
    {
        if ($conn->getDriver() instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\SQLite\Driver) {
            throw new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception('Cannot use TableGenerator with SQLite.');
        }
        $this->conn = \_PhpScoper3fe455fa007d\Doctrine\DBAL\DriverManager::getConnection($conn->getParams(), $conn->getConfiguration(), $conn->getEventManager());
        $this->generatorTableName = $generatorTableName;
    }
    /**
     * Generates the next unused value for the given sequence name.
     *
     * @param string $sequence
     *
     * @return int
     *
     * @throws Exception
     */
    public function nextValue($sequence)
    {
        if (isset($this->sequences[$sequence])) {
            $value = $this->sequences[$sequence]['value'];
            $this->sequences[$sequence]['value']++;
            if ($this->sequences[$sequence]['value'] >= $this->sequences[$sequence]['max']) {
                unset($this->sequences[$sequence]);
            }
            return $value;
        }
        $this->conn->beginTransaction();
        try {
            $platform = $this->conn->getDatabasePlatform();
            $sql = 'SELECT sequence_value, sequence_increment_by' . ' FROM ' . $platform->appendLockHint($this->generatorTableName, \_PhpScoper3fe455fa007d\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE) . ' WHERE sequence_name = ? ' . $platform->getWriteLockSQL();
            $row = $this->conn->fetchAssociative($sql, [$sequence]);
            if ($row !== \false) {
                $row = \array_change_key_case($row, \CASE_LOWER);
                $value = $row['sequence_value'];
                $value++;
                \assert(\is_int($value));
                if ($row['sequence_increment_by'] > 1) {
                    $this->sequences[$sequence] = ['value' => $value, 'max' => $row['sequence_value'] + $row['sequence_increment_by']];
                }
                $sql = 'UPDATE ' . $this->generatorTableName . ' ' . 'SET sequence_value = sequence_value + sequence_increment_by ' . 'WHERE sequence_name = ? AND sequence_value = ?';
                $rows = $this->conn->executeStatement($sql, [$sequence, $row['sequence_value']]);
                if ($rows !== 1) {
                    throw new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception('Race-condition detected while updating sequence. Aborting generation');
                }
            } else {
                $this->conn->insert($this->generatorTableName, ['sequence_name' => $sequence, 'sequence_value' => 1, 'sequence_increment_by' => 1]);
                $value = 1;
            }
            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            throw new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception('Error occurred while generating ID with TableGenerator, aborted generation: ' . $e->getMessage(), 0, $e);
        }
        return $value;
    }
}