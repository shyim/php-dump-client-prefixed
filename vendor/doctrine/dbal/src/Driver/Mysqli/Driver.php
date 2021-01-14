<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractMySQLDriver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\HostRequired;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer\Charset;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer\Options;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer\Secure;
use function count;
final class Driver extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractMySQLDriver
{
    /**
     * {@inheritdoc}
     *
     * @return Connection
     */
    public function connect(array $params)
    {
        if (!empty($params['persistent'])) {
            if (!isset($params['host'])) {
                throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception\HostRequired::forPersistentConnection();
            }
            $host = 'p:' . $params['host'];
        } else {
            $host = $params['host'] ?? null;
        }
        $flags = null;
        $preInitializers = $postInitializers = [];
        if (isset($params['driverOptions'])) {
            $driverOptions = $params['driverOptions'];
            if (isset($driverOptions[\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Connection::OPTION_FLAGS])) {
                $flags = $driverOptions[\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Connection::OPTION_FLAGS];
                unset($driverOptions[\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Connection::OPTION_FLAGS]);
            }
            $preInitializers = $this->withOptions($preInitializers, $driverOptions);
        }
        $preInitializers = $this->withSecure($preInitializers, $params);
        $postInitializers = $this->withCharset($postInitializers, $params);
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Connection($host, $params['user'] ?? null, $params['password'] ?? null, $params['dbname'] ?? null, $params['port'] ?? null, $params['unix_socket'] ?? null, $flags, $preInitializers, $postInitializers);
    }
    /**
     * @param list<Initializer> $initializers
     * @param array<int,mixed>  $options
     *
     * @return list<Initializer>
     */
    private function withOptions(array $initializers, array $options) : array
    {
        if (\count($options) !== 0) {
            $initializers[] = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer\Options($options);
        }
        return $initializers;
    }
    /**
     * @param list<Initializer>   $initializers
     * @param array<string,mixed> $params
     *
     * @return list<Initializer>
     */
    private function withSecure(array $initializers, array $params) : array
    {
        if (isset($params['ssl_key']) || isset($params['ssl_cert']) || isset($params['ssl_ca']) || isset($params['ssl_capath']) || isset($params['ssl_cipher'])) {
            $initializers[] = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer\Secure($params['ssl_key'] ?? '', $params['ssl_cert'] ?? '', $params['ssl_ca'] ?? '', $params['ssl_capath'] ?? '', $params['ssl_cipher'] ?? '');
        }
        return $initializers;
    }
    /**
     * @param list<Initializer>   $initializers
     * @param array<string,mixed> $params
     *
     * @return list<Initializer>
     */
    private function withCharset(array $initializers, array $params) : array
    {
        if (isset($params['charset'])) {
            $initializers[] = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Initializer\Charset($params['charset']);
        }
        return $initializers;
    }
}
