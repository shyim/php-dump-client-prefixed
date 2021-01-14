<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message;

use _PhpScoper3fe455fa007d\PhpDumpClient\Struct;
class Origin extends \_PhpScoper3fe455fa007d\PhpDumpClient\Struct
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
