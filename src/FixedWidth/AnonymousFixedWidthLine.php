<?php

namespace TeamZac\Parsing\FixedWidth;

use Exception;
use TeamZac\Parsing\FixedWidth\Field;
use TeamZac\Parsing\Contracts\LineDefinition;

class AnonymousFixedWidthLine implements LineDefinition
{
    /** @var array */
    protected $definitions = [];

    public function __construct($definitions = [])
    {
        foreach ($definitions as $field) {
            if (! $field instanceof Field) {
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
