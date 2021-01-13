<?php

namespace _PhpScoper926b1169e332\PhpDumpClient;

class Struct implements \JsonSerializable
{
    public function jsonSerialize() : array
    {
        return \get_object_vars($this);
    }
}
