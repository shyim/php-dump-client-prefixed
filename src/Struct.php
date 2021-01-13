<?php

namespace _PhpScoper3fe455fa007d\PhpDumpClient;

class Struct implements \JsonSerializable
{
    public function jsonSerialize() : array
    {
        return \get_object_vars($this);
    }
}
