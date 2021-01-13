<?php



use _PhpScopereaa8bfd44f12\PhpDumpClient\Client;
if (!\function_exists('pd')) {
    function pd() : \_PhpScopereaa8bfd44f12\PhpDumpClient\Client
    {
        static $client;
        if ($client === null) {
            $client = new \_PhpScopereaa8bfd44f12\PhpDumpClient\Client();
        }
        return $client;
    }
}
