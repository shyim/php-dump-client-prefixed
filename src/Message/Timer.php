<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message;

use _PhpScoper3fe455fa007d\PhpDumpClient\Client;
use _PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\HtmlPayload;
use _PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\TablePayload;
class Timer
{
    private float $time;
    private int $memoryUsage;
    private int $peakMemoryUsage;
    private \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\TablePayload $table;
    private \_PhpScoper3fe455fa007d\PhpDumpClient\Client $client;
    private \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Message $message;
    private string $title;
    public function __construct(string $title, \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Message $message, \_PhpScoper3fe455fa007d\PhpDumpClient\Client $client)
    {
        $this->time = \microtime(\true);
        $this->memoryUsage = \memory_get_usage(\true);
        $this->peakMemoryUsage = \memory_get_peak_usage(\true);
        $this->table = new \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\TablePayload(['Time', 'Total Memory Usage', 'Peak Memory Usage']);
        $this->client = $client;
        $this->message = $message;
        $this->title = $title;
        $this->message->payload(new \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\HtmlPayload($title));
        $this->message->payload($this->table);
    }
    public function checkpoint() : self
    {
        return $this->track();
    }
    public function stop() : \_PhpScoper3fe455fa007d\PhpDumpClient\Client
    {
        $this->track();
        $this->client->send($this->message);
        return $this->client;
    }
    protected function track() : self
    {
        $currentMemoryUsage = \memory_get_usage(\true);
        $currentPeakMemoryUsage = \memory_get_peak_usage(\true);
        $this->table->addRow((string) (\microtime(\true) - $this->time), $this->formatBytes($currentMemoryUsage), $this->formatBytes($currentPeakMemoryUsage));
        $this->time = \microtime(\true);
        $this->memoryUsage = $currentMemoryUsage;
        $this->peakMemoryUsage = $currentPeakMemoryUsage;
        return $this;
    }
    private function formatBytes($size, $precision = 2) : string
    {
        $base = \log($size, 1024);
        $suffixes = ['', 'Kb', 'Mb', 'Gb', 'Tb'];
        return \round(\pow(1024, $base - \floor($base)), $precision) . ' ' . $suffixes[\floor($base)];
    }
}
