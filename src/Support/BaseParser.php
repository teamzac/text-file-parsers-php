<?php

namespace TeamZac\Parsing\Support;

use TeamZac\Parsing\Contracts\TextParser;
use TeamZac\Parsing\Exceptions\MissingLineDefinitionException;
use TeamZac\Parsing\Exceptions\NoFileToParseException;
use TeamZac\Parsing\Support\AnonymousLineDefinition;

class BaseParser implements TextParser
{
    /** @var mixed */
    protected $definition;

    /** @var string */
    protected $filepath;

    /** @var bool */
    protected $hasHeaders = false;

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
            $this->definition = new AnonymousLineDefinition($definition);
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
     * @{inheritdoc}
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
     * @{inheritdoc}
    */
    public function each($callback) 
    {
    	$this->verifyFilepathWasGiven()
    		->verifyLineDefinitionExists();

        $file = fopen($this->filepath, 'r');
        $skippedHeaders = false;
        while ($line = fgets($file)) {
            if ($this->hasHeaders && !$skippedHeaders) {
                $skippedHeaders = true;
                continue;
            }

            $callback($this->parseLine($line));
        }
        fclose($file);
    }

    /**
     * @{inheritdoc}
     */
    public function parseLine($text): ParsedLine
    {
    	//
    }

    /**
     * Verify that a filepath was given
     *
     * @throws NoFileToParseException
     */
    protected function verifyFilepathWasGiven()
    {
        if (is_null($this->filepath)) {
            NoFileToParseException::throw();
        }

        return $this;
    }

    /** 
     * Verify the LineDefinition has been provided
     *
     * @throws MissingLineDefinitionException
     */
    protected function verifyLineDefinitionExists()
    {
        if (is_null($this->definition)) {
            MissingLineDefinitionException::throw();
        }

        return $this;
    }
}
