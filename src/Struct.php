<?php

namespace _PhpScoper5aadddf2c2bd\PhpDumpClient;

class Struct implements \JsonSerializable
{
    public function jsonSerialize() : array
    {
        return \get_object_vars($this);
    }
}
