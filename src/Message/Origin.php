<?php

namespace _PhpScoper926b1169e332\PhpDumpClient\Message;

use _PhpScoper926b1169e332\PhpDumpClient\Struct;
class Origin extends \_PhpScoper926b1169e332\PhpDumpClient\Struct
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
