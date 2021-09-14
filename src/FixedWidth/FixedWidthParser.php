<?php

namespace TeamZac\Parsing\FixedWidth;

use Illuminate\Support\Arr;
use TeamZac\Parsing\Exceptions\CouldNotParseException;
use TeamZac\Parsing\Support\BaseParser;
use TeamZac\Parsing\Support\ParsedLine;

class FixedWidthParser extends BaseParser
{
    /** @var string */
    protected $definition;

    /** @var string */
    protected $filepath;

    /** @var int */
    protected $skip = 0;

    /**
     * Static constructor
     */
    public static function make()
    {
        return new static();
    }

    /**
     * Set the row parser to use 
     *
     * @param   string|object|array $definition
     * @return  $this
     */
    public function using($definition)
    {
        if (is_string($definition)) {
            $this->definition = resolve($definition);
        } else if (is_object($definition)) {
            $this->definition = $definition;
        } else if (is_array($definition)) {
            $this->definition = new AnonymousFixedWidthLine($definition);
        }

        return $this;
    }

    /**
     * Set the number of lines to skip
     *
     * @var int $skip
     * @return $this
     */
    public function skip($skip)
    {
        $this->skip = $skip;
        return $this;
    }

    /** 
     * Set the path of the file to parse
     *
     * @var string $filepath
     * @return $this
     */
    public function parse($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * Get all records in one swoop. If your file isn't
     * too large, you might choose to use this method
     * instead of iterating one at a time.
     *
     * @return array
    */
    public function all()
    {
        $values = [];

        $this->each(function($line) use (&$values) {
            $values[] = $line;
        });

        return $values;
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function parseLine($line): ParsedLine
    {
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
