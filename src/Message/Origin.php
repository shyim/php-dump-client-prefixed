<?php

namespace _PhpScopereaa8bfd44f12\PhpDumpClient\Message;

use _PhpScopereaa8bfd44f12\PhpDumpClient\Struct;
class Origin extends \_PhpScopereaa8bfd44f12\PhpDumpClient\Struct
{
    protected string $fileName;
    protected int $lineNumber;
    public function __construct(string $fileName, int $lineNumber)
    {
        $this->fileName = $fileName;
        $this->lineNumber = $lineNumber;
    }
    public function getFileName() : string
    {
        return $this->fileName;
    }
    public function getLineNumber() : int
    {
        return $this->lineNumber;
    }
}
