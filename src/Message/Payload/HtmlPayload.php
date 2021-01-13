<?php

namespace _PhpScoper926b1169e332\PhpDumpClient\Message\Payload;

class HtmlPayload extends \_PhpScoper926b1169e332\PhpDumpClient\Message\Payload\AbstractPayload
{
    protected string $type = 'html';
    protected string $content;
    public function __construct(string $html)
    {
        $this->content = $html;
    }
}
