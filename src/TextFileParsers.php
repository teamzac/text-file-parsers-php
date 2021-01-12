<?php

namespace TeamZac\Parsing;

use TeamZac\Parsing\Delimited\DelimitedParser;
use TeamZac\Parsing\FixedWidth\FixedWidthParser;

class TextFileParsers
{
    public function fixedWidth()
    {
        return new FixedWidthParser();
    }

    public function delimited($delimiter = ',')
    {
        return new DelimitedParser($delimiter);
    }
}
