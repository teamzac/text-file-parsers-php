<?php

namespace TeamZac\Parsing;

use TeamZac\Parsing\FixedWidth\FixedWidthParser;

class TextFileParsers
{
    public function fixedWidth()
    {
        return resolve(FixedWidthParser::class);
    }
}
