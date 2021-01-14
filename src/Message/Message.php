<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message;

use _PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\AbstractPayload;
use _PhpScoper3fe455fa007d\PhpDumpClient\Struct;
use _PhpScoper3fe455fa007d\PhpDumpClient\Uuid;
class Message extends \_PhpScoper3fe455fa007d\PhpDumpClient\Struct
{
    protected string $uuid;
    protected \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Origin $origin;
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
        $this->uuid = \_PhpScoper3fe455fa007d\PhpDumpClient\Uuid::randomHex();
        $this->time = \time();
        $this->origin = new \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Origin($fileName, $lineNumber);
    }
    public function tag(string ...$tag) : self
    {
        $this->tags = [...$this->tags, ...$tag];
        return $this;
    }
    public function payload(\_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\AbstractPayload $payload) : self
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
