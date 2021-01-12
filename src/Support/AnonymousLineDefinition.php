<?php

namespace TeamZac\Parsing\Support;

use TeamZac\Parsing\Contracts\LineDefinition;
use TeamZac\Parsing\Support\BaseField;

class AnonymousLineDefinition implements LineDefinition
{
    /** @var array */
    protected $definitions = [];

    public function __construct($definitions = [])
    {
        foreach ($definitions as $field) {
            if (! $field instanceof BaseField) {
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
