<?php

namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload;

class HtmlPayload extends \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\AbstractPayload
{
    protected string $type = 'html';
    protected string $content;
    public function __construct(string $html)
    {
        $this->content = $html;
    }
}
