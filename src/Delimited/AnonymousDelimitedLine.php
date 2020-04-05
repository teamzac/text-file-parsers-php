<?php

namespace TeamZac\Parsing\Delimited;

use Exception;
use TeamZac\Parsing\Support\TextField;
use TeamZac\Parsing\Contracts\LineDefinition;

class AnonymousDelimitedLine implements LineDefinition
{
    /** @var array */
    protected $definitions = [];

    public function __construct($definitions = [])
    {
        foreach ($definitions as $field) {
            if (! $field instanceof TextField) {
                throw new Exception('You did not provide a Field object to define how to parse the line');
            }
        }

        $this->definitions = $definitions;
    }

    /** 
     * @{inheritdoc}
     */
    public function fieldDefinitions(): array
    {
        return $this->definitions;
    }
}