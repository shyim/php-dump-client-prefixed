<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload;

class CodePayload extends \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\AbstractPayload
{
    /**
     * @var string
     */
    protected $type = 'code';
    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $language;
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
