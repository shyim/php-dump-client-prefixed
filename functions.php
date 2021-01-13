<?php

require __DIR__ . '/vendor/autoload.php';

use _PhpScoper3fe455fa007d\PhpDumpClient\Client;
if (!\function_exists('pd')) {
    function pd() : \_PhpScoper3fe455fa007d\PhpDumpClient\Client
    {
        static $client;
        if ($client === null) {
            $client = new \_PhpScoper3fe455fa007d\PhpDumpClient\Client();
        }
        return $client;
    }
}
