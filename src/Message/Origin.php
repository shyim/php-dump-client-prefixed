<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\PhpDumpClient\Message;

use _PhpScoper3fe455fa007d\PhpDumpClient\Struct;
class Origin extends \_PhpScoper3fe455fa007d\PhpDumpClient\Struct
{
    /**
     * @var string
     */
    protected $fileName;
    /**
     * @var int
     */
    protected $lineNumber;
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
