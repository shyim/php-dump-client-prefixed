<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload;

class HtmlPayload extends \_PhpScoper3fe455fa007d\PhpDumpClient\Message\Payload\AbstractPayload
{
    /**
     * @var string
     */
    protected $type = 'html';
    /**
     * @var string
     */
    protected $content;
    public function __construct(string $html)
    {
        $this->content = $html;
    }
}
