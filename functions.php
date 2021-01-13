<?php



use _PhpScoper5aadddf2c2bd\PhpDumpClient\Client;
if (!\function_exists('pd')) {
    function pd() : \_PhpScoper5aadddf2c2bd\PhpDumpClient\Client
    {
        static $client;
        if ($client === null) {
            $client = new \_PhpScoper5aadddf2c2bd\PhpDumpClient\Client();
        }
        return $client;
    }
}
