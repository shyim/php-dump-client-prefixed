<?php

namespace _PhpScopereaa8bfd44f12\PhpDumpClient\Message\Payload;

class HtmlPayload extends \_PhpScopereaa8bfd44f12\PhpDumpClient\Message\Payload\AbstractPayload
{
    protected string $type = 'html';
    protected string $content;
    public function __construct(string $html)
    {
        $this->content = $html;
    }
}
