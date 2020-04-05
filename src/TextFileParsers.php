<?php

namespace TeamZac\Parsing;

use TeamZac\Parsing\Delimited\DelimitedParser;
use TeamZac\Parsing\FixedWidth\FixedWidthParser;

class TextFileParsers
{
    public function fixedWidth()
    {
        return FixedWidthParser::make();
    }

    public function delimited($delimiter = ',')
    {
        return DelimitedParser::make($delimiter);
    }
}
