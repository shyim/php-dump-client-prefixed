<?php

namespace _PhpScopereaa8bfd44f12\PhpDumpClient\Message;

use _PhpScopereaa8bfd44f12\PhpDumpClient\Message\Payload\AbstractPayload;
use _PhpScopereaa8bfd44f12\PhpDumpClient\Struct;
use _PhpScopereaa8bfd44f12\PhpDumpClient\Uuid;
class Message extends \_PhpScopereaa8bfd44f12\PhpDumpClient\Struct
{
    protected string $uuid;
    protected \_PhpScopereaa8bfd44f12\PhpDumpClient\Message\Origin $origin;
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
        $this->uuid = \_PhpScopereaa8bfd44f12\PhpDumpClient\Uuid::randomHex();
        $this->time = \time();
        $this->origin = new \_PhpScopereaa8bfd44f12\PhpDumpClient\Message\Origin($fileName, $lineNumber);
    }
    public function tag(string ...$tag) : self
    {
        $this->tags = [...$this->tags, ...$tag];
        return $this;
    }
    function payload(\_PhpScopereaa8bfd44f12\PhpDumpClient\Message\Payload\AbstractPayload $payload) : self
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
