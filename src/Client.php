<?php

namespace _PhpScoper926b1169e332\PhpDumpClient;

use _PhpScoper926b1169e332\Doctrine\SqlFormatter\NullHighlighter;
use _PhpScoper926b1169e332\Doctrine\SqlFormatter\SqlFormatter;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Message;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Payload\ClearPayload;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Payload\CodePayload;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Payload\HtmlPayload;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Payload\PausePayload;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Payload\TablePayload;
use _PhpScoper926b1169e332\PhpDumpClient\Message\Timer;
use _PhpScoper926b1169e332\Symfony\Component\VarDumper\Cloner\VarCloner;
use _PhpScoper926b1169e332\Symfony\Component\VarDumper\Dumper\HtmlDumper;
class Client
{
    private string $instanceUrl;
    private array $tags = [];
    public function __construct()
    {
        $this->instanceUrl = $_SERVER['PHP_DUMP_SERVER_URL'] ?? 'http://localhost:9009';
    }
    /**
     * Allows setting a custom server at runtime. Prefer environment variable
     */
    public function setServerUrl(string $url) : void
    {
        $this->instanceUrl = $url;
    }
    public function log(...$arguments) : self
    {
        $msg = $this->createMessage();
        $cloner = new \_PhpScoper926b1169e332\Symfony\Component\VarDumper\Cloner\VarCloner();
        $cloner->setMaxItems(-1);
        $htmlDumper = new \_PhpScoper926b1169e332\Symfony\Component\VarDumper\Dumper\HtmlDumper();
        foreach ($arguments as $argument) {
            $data = $htmlDumper->dump($cloner->cloneVar($argument), \true);
            $msg->payload(new \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\HtmlPayload($data));
        }
        $this->send($msg);
        return $this;
    }
    public function trace() : self
    {
        $backtraces = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        if (\count($backtraces) <= 1) {
            return $this;
        }
        $backtraces = \array_slice($backtraces, 1);
        $table = new \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\TablePayload(['File', 'Function']);
        foreach ($backtraces as $backtrace) {
            $table->addRow(\sprintf('%s:%s', $this->stripPath($backtrace['file']), $backtrace['line']), $backtrace['function']);
        }
        $msg = $this->createMessage();
        $msg->payload($table);
        $this->send($msg);
        return $this;
    }
    public function clear() : self
    {
        $msg = $this->createMessage();
        $msg->payload(new \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\ClearPayload());
        $this->send($msg);
        return $this;
    }
    public function time(string $title, ?callable $func = null) : \_PhpScoper926b1169e332\PhpDumpClient\Message\Timer
    {
        $t = new \_PhpScoper926b1169e332\PhpDumpClient\Message\Timer($title, $this->createMessage(), $this);
        if ($func === null) {
            return $t;
        }
        $func();
        $t->stop();
        return $t;
    }
    public function tag(string ...$tag) : self
    {
        $tagInstance = clone $this;
        $tagInstance->tags = [...$tagInstance->tags, ...$tag];
        return $tagInstance;
    }
    public function pause(?string $title = null) : self
    {
        $msg = $this->createMessage();
        if ($title) {
            $msg->payload(new \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\HtmlPayload($title));
        }
        $msg->payload(new \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\PausePayload());
        $this->send($msg);
        while ($this->lockExists($msg->getId())) {
            \sleep(1);
        }
        return $this;
    }
    public function logSql(string $sql) : self
    {
        $msg = $this->createMessage();
        $msg->payload(new \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\CodePayload((new \_PhpScoper926b1169e332\Doctrine\SqlFormatter\SqlFormatter(new \_PhpScoper926b1169e332\Doctrine\SqlFormatter\NullHighlighter()))->format($sql), 'sql'));
        $this->send($msg);
        return $this;
    }
    protected function createMessage() : \_PhpScoper926b1169e332\PhpDumpClient\Message\Message
    {
        $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        return new \_PhpScoper926b1169e332\PhpDumpClient\Message\Message($this->stripPath($backtrace[1]['file']), $backtrace[1]['line']);
    }
    public function send(\_PhpScoper926b1169e332\PhpDumpClient\Message\Message $message) : void
    {
        $message->tag(...$this->tags);
        $ch = \curl_init($this->instanceUrl . '/client');
        \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'POST');
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, \json_encode($message));
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, \true);
        $headers = ['pd-id:' . $message->getId()];
        if ($message->hasPayload(\_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\PausePayload::class)) {
            $headers[] = 'pd-action:pause';
        }
        \curl_setopt($ch, \CURLOPT_HTTPHEADER, $headers);
        \curl_exec($ch);
        \curl_close($ch);
    }
    private function lockExists(string $id) : bool
    {
        $ch = \curl_init($this->instanceUrl . '/is-locked');
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, \true);
        $headers = ['pd-id:' . $id];
        \curl_setopt($ch, \CURLOPT_HTTPHEADER, $headers);
        $resp = \curl_exec($ch);
        \curl_close($ch);
        return $resp === "1";
    }
    private function stripPath(string $path) : string
    {
        $currentFolder = \getcwd();
        if (\strpos($path, $currentFolder) === 0) {
            return \substr($path, \strlen($currentFolder) + 1);
        }
        return $path;
    }
}
