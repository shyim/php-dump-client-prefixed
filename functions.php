<?php



use _PhpScoper926b1169e332\PhpDumpClient\Client;
if (!\function_exists('pd')) {
    function pd() : \_PhpScoper926b1169e332\PhpDumpClient\Client
    {
        static $client;
        if ($client === null) {
            $client = new \_PhpScoper926b1169e332\PhpDumpClient\Client();
        }
        return $client;
    }
}
