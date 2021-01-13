<?php

namespace _PhpScoper5aadddf2c2bd\PhpDumpClient\Message;

use _PhpScoper5aadddf2c2bd\PhpDumpClient\Struct;
class Origin extends \_PhpScoper5aadddf2c2bd\PhpDumpClient\Struct
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
