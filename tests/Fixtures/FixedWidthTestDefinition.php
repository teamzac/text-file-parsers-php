<?php

namespace TeamZac\Parsing\Tests\Fixtures;

use TeamZac\Parsing\FixedWidth\Field;
use TeamZac\Parsing\Contracts\LineDefinition;

class FixedWidthTestDefinition implements LineDefinition
{
    public function fieldDefinitions(): array
    {
        return [
            Field::make('id', 5)->asInt(),
            Field::make('name', 10),
            Field::make('email', 20),
            Field::make('active', 1)->map([
                'y' => true,
                'n' => false
            ]),
            Field::make('favorite_colors', 20)->explode('|'),
            Field::make('salary', 9)->asFloat(),
            Field::make('address.uppercased', 20)->transformWith(function($value) {
                return strtoupper($value);
            }),
        ];
    }
}
