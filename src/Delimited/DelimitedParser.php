<?php

namespace TeamZac\Parsing\Delimited;

use Illuminate\Support\Arr;
use TeamZac\Parsing\Support\BaseParser;
use TeamZac\Parsing\Support\ParsedLine;

class DelimitedParser extends BaseParser
{
    /** @var string */
    protected $delimiter = ',';

    /** @var mixed */
    protected $definition;

    /** @var string */
    protected $filepath;

    /** @var bool */
    protected $hasHeaders = false;

    public function __construct($delimiter = ',') 
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Static constructor
     */
    public static function make($delimiter = ',') 
    {
        return new static($delimiter);
    }

    /**
     * @{inheritdoc}
     */
    public function parseLine($text): ParsedLine
    {
        $this->verifyLineDefinitionExists();

        $columns = str_getcsv($text, $this->delimiter);
        $attributes = [];
        foreach ($this->definition->fieldDefinitions() as $index => $field) {
            if ($field->shouldBeIncluded()) {
                Arr::set(
                    $attributes, 
                    $field->getKey(), 
                    $field->getCastedValue($columns[$index])
                );
            }
        }

        return new ParsedLine($attributes);
    }
}
