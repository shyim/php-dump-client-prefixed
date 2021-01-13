<?php

namespace _PhpScopereaa8bfd44f12\PhpDumpClient;

class Struct implements \JsonSerializable
{
    public function jsonSerialize() : array
    {
        return \get_object_vars($this);
    }
}
