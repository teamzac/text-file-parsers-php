<?php

namespace TeamZac\Parsing\FixedWidth;

use Illuminate\Support\Arr;
use TeamZac\Parsing\Support\BaseParser;
use TeamZac\Parsing\Support\ParsedLine;

class FixedWidthParser extends BaseParser
{
    /**
     * Static constructor
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @{inheritdoc}
     */
    public function parseLine($line): ParsedLine
    {
        $this->verifyLineDefinitionExists();
        
        $columnPointer = 0;
        $attributes = [];
        foreach ($this->definition->fieldDefinitions() as $field) {
            if ($field->shouldBeIncluded()) {
                Arr::set(
                    $attributes, 
                    $field->getKey(), 
                    $field->getCastedValue(substr($line, $columnPointer, $field->getLength()))
                );
            }

            $columnPointer += $field->getLength();
        }

        return new ParsedLine($attributes);
    }
}
