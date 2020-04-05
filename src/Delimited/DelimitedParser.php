<?php

namespace TeamZac\Parsing\Delimited;

use Illuminate\Support\Arr;
use TeamZac\Parsing\Support\ParsedLine;
use TeamZac\Parsing\Exceptions\CouldNotParseException;

class DelimitedParser
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
            $this->definition = new AnonymousDelimitedLine($definition);
        }

        return $this;
    }

    /**
     * Skip the first line because they are headers
     * 
     * @return  $this
     */
    public function hasHeaders()
    {
        $this->hasHeaders = true;
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
     * Parse the file and return each record one at a time.
     * Definitely use this for larger files instead of all()
     *
     * @param   Callable $callback
     */
    public function each($callback) 
    {
        if (is_null($this->filepath)) {
            throw CouldNotParseException::noFile();
        }

        if (is_null($this->definition)) {
            throw CouldNotParseException::noLineDefinition();
        }

        $file = fopen($this->filepath, 'r');
        $count = 0;
        while ($line = fgets($file)) {
            if ($count == 0 && $this->hasHeaders) {
                $count++;
                continue;
            }

            $callback($this->parseLine($line));
        }
        fclose($file);
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function parseLine($text)
    {
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
