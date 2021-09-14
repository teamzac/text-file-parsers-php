<?php

namespace TeamZac\Parsing\Tests\Fixtures;

use TeamZac\Parsing\Delimited\Field;
use TeamZac\Parsing\Contracts\LineDefinition;

class DelimitedTestDefinition implements LineDefinition
{
    public function fieldDefinitions(): array
    {
        return [
            Field::make('type'),
            Field::make('year'),
            Field::make('account_number'),
            Field::make('record_type'),
            Field::make('sequence_number'),
            Field::make('pidn'),
            Field::make('owner_name'),
        ];
    }
}
