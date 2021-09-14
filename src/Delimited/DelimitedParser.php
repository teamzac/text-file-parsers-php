<?php

namespace TeamZac\Parsing\Delimited;

use Illuminate\Support\Arr;
use TeamZac\Parsing\Support\BaseParser;
use TeamZac\Parsing\Support\ParsedLine;
use TeamZac\Parsing\Exceptions\CouldNotParseException;

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

    /** @var int */
    protected $skip = 0;

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
     * Parse the file and return each record one at a time.
     * Definitely use this for larger files instead of all()
     *
     * @param   Callable $callback
     */
    public function each($callback) 
    {
        $this->verifyFilepathWasGiven()
            ->verifyLineDefinitionExists();

        $file = fopen($this->filepath, 'r');
        $count = 0;
        $i = 0;
        while ($line = fgets($file)) {
            if ($count == 0 && $this->hasHeaders) {
                $count++;
                continue;
            }

            if ($i < $this->skip) {
                $i++;
                continue;
            }

            $callback($this->parseLine($line));
            unset($line);
        }
        fclose($file);
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function parseLine($text): ParsedLine
    {
        $columns = str_getcsv($text, $this->delimiter);

        $attributes = [];
        $definitions = $this->definition->fieldDefinitions();
        foreach ($definitions as $index => $field) {
            if ($field->shouldBeIncluded()) {
                try {
                    Arr::set(
                        $attributes, 
                        $field->getKey(), 
                        $field->getCastedValue($columns[$index])
                    );
                } catch (\Exception $e) {
                    dd($e->getMessage(), $index, $columns);
                }
            }
        }

        unset($columns, $definitions);
        return new ParsedLine($attributes);
    }
}
