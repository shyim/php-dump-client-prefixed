<?php

namespace _PhpScoper5aadddf2c2bd\PhpDumpClient\Message;

use _PhpScoper5aadddf2c2bd\PhpDumpClient\Message\Payload\AbstractPayload;
use _PhpScoper5aadddf2c2bd\PhpDumpClient\Struct;
use _PhpScoper5aadddf2c2bd\PhpDumpClient\Uuid;
class Message extends \_PhpScoper5aadddf2c2bd\PhpDumpClient\Struct
{
    protected string $uuid;
    protected \_PhpScoper5aadddf2c2bd\PhpDumpClient\Message\Origin $origin;
    protected int $time;
    /**
     * @var string[]
     */
    protected array $tags = [];
    /**
     * @var AbstractPayload[]
     */
    protected array $payloads = [];
    public function __construct(string $fileName, int $lineNumber)
    {
        $this->uuid = \_PhpScoper5aadddf2c2bd\PhpDumpClient\Uuid::randomHex();
        $this->time = \time();
        $this->origin = new \_PhpScoper5aadddf2c2bd\PhpDumpClient\Message\Origin($fileName, $lineNumber);
    }
    public function tag(string ...$tag) : self
    {
        $this->tags = [...$this->tags, ...$tag];
        return $this;
    }
    function payload(\_PhpScoper5aadddf2c2bd\PhpDumpClient\Message\Payload\AbstractPayload $payload) : self
    {
        $this->payloads[] = $payload;
        return $this;
    }
    public function hasPayload(string $payloadClass) : bool
    {
        foreach ($this->payloads as $payload) {
            if ($payload instanceof $payloadClass) {
                return \true;
            }
        }
        return \false;
    }
    public function getId() : string
    {
        return $this->uuid;
    }
}
