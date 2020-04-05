<?php

namespace TeamZac\Parsing\Exceptions;

use RuntimeException;

class CouldNotParseException extends RuntimeException
{
    public static function noFile()
    {
        return new static('You must set a filename before parsing can occur. Be sure to call the parse() method.');
    }

    public static function noLineDefinition()
    {
        return new static('You must provide a line definition with the using() method before parsing can occur.');
    }

    public static function noParsingStrategy()
    {
        return new static('Your line definition must provide a parsing strategy.');
    }
}
