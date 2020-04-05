<?php

namespace TeamZac\Parsing\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use TeamZac\Parsing\Facades\Parsing;
use TeamZac\Parsing\Delimited\Field;
use TeamZac\Parsing\TextFileParsersServiceProvider;
use TeamZac\Parsing\Tests\Fixtures\FixedWidthTestDefinition;

class DelimitedParsingTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TextFileParsersServiceProvider::class];
    }

    /** @test */
    public function it_parses_from_dedicated_definition_class()
    {
        $values = Parsing::delimited('|')
            ->using([
                Field::make('rp'),
                Field::int('appraisal_year'),
                Field::int('account.number'),
                Field::make('account.record_type'),
                Field::int('account.sequence_number'),
                Field::int('account.pidn'),
                Field::make('owner.name'),
                Field::make('owner.address.street'),
                Field::make('owner.address.city_state'),
                Field::make('owner.address.zip'),
                Field::ignored('owner_zip4'),
                Field::make('owner.crrt'),
                Field::make('situs.address'),
                Field::make('property_class'),
                Field::make('maps.tad'),
                Field::make('maps.mapsco'),
                Field::make('codes.exemption'),
                Field::make('codes.state_use'),
                Field::make('legal.description'),
                Field::make('legal.notice_date'),
                Field::int('districts.county'),
                Field::int('districts.city'),
                Field::int('districts.school'),
                Field::ignored(''),
                Field::int('districts.special_1'),
                Field::int('districts.special_2'),
                Field::int('districts.special_3'),
                Field::int('districts.special_4'),
                Field::int('districts.special_5'),
                Field::make('deed.date'),
                Field::make('deed.book'),
                Field::make('deed.page'),
                Field::make('values.land'),
                Field::make('values.improvement'),
                Field::make('values.total'),
                Field::make('improvements.garage_capacity'),
                Field::make('improvements.num_bedrooms'),
                Field::make('improvements.num_bathrooms'),
                Field::int('year_built'),
                Field::make('improvements.living_area'),
                Field::make('improvements.swimming_pool'),
                Field::ignored('arb_indicator'),
                Field::make('codes.ag'),
                Field::make('land.acres'),
                Field::make('land.square_footage'),
                Field::make('ag.acres'),
                Field::make('ag.value'),
                Field::make('improvements.central_heat')->map([
                    'Y' => true, 'N' => false,
                ]),
                Field::make('improvements.central_air')->map([
                    'Y' => true, 'N' => false,
                ]),
                Field::make('improvements.structure_count'),
                Field::ignored('from_accounts'),
                Field::make('appraisal.date'),
                Field::make('appraisal.value'),
            ])
            ->hasHeaders()
            ->parse(__DIR__.'/Fixtures/delimited-test-file.txt')
            ->each(function($line) {
                dd($line);
            });
    }
}
