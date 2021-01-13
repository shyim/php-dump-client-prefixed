<?php

namespace _PhpScoper5aadddf2c2bd\PhpDumpClient\Message\Payload;

class CodePayload extends \_PhpScoper5aadddf2c2bd\PhpDumpClient\Message\Payload\AbstractPayload
{
    protected string $type = 'code';
    protected string $code;
    protected string $language;
    public function __construct(string $code, string $language = 'text')
    {
        $this->code = $code;
        $this->language = $language;
    }
    public function jsonSerialize() : array
    {
        return ['type' => $this->type, 'content' => ['value' => $this->code, 'language' => $this->language]];
    }
}
