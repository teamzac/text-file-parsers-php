<?php

namespace TeamZac\Parsing\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use TeamZac\Parsing\Exceptions\MissingLineDefinitionException;
use TeamZac\Parsing\Exceptions\NoFileToParseException;
use TeamZac\Parsing\Facades\Parsing;
use TeamZac\Parsing\FixedWidth\Field;
use TeamZac\Parsing\FixedWidth\FixedWidthParser;
use TeamZac\Parsing\Tests\Fixtures\FixedWidthTestDefinition;
use TeamZac\Parsing\TextFileParsersServiceProvider;

class FixedWidthParsingTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TextFileParsersServiceProvider::class];
    }
    
    /** @test */
    public function it_performs_simple_parsing()
    {
        $rowText = '  A  B  C';

        $line = Parsing::fixedWidth()
            ->using([
                Field::make('a', 3),
                Field::make('b', 3),
                Field::make('c', 3)->untrimmed(),
            ])
            ->parseLine($rowText);

        $this->assertSame('A', $line->get('a'));
        $this->assertSame('B', $line->get('b'));
        $this->assertSame('  C', $line->get('c'));
    }
    
    /** @test */
    public function it_casts_the_raw_values()
    {
        $rowText = '  1  23.0  1';

        $line = Parsing::fixedWidth()
            ->using([
                Field::make('as_string', 3),
                Field::make('as_int', 3)->asInt(),
                Field::make('as_float', 3)->asFloat(),
                Field::make('as_bool', 3)->asBool(),
            ])
            ->parseLine($rowText);

        $this->assertSame('1', $line->get('as_string'));
        $this->assertSame(2, $line->get('as_int'));
        $this->assertSame(3.0, $line->get('as_float'));
        $this->assertSame(true, $line->get('as_bool'));
    }
    
    /** @test */
    public function it_casts_with_custom_date_formats()
    {
        $rowText = '2000-01-0102/01/2000';

        $line = Parsing::fixedWidth()
            ->using([
                Field::make('begin', 10)->asDate('Y-m-d'),
                Field::date('end', 10, 'm/d/Y'),
            ])
            ->parseLine($rowText);

        $this->assertInstanceOf(Carbon::class, $line->begin);
        $this->assertSame('2000-01-01', $line->begin->format('Y-m-d'));

        $this->assertInstanceOf(Carbon::class, $line->end);
        $this->assertSame('2000-02-01', $line->end->format('Y-m-d'));
    }
    
    /** @test */
    public function it_uses_a_value_map()
    {
        $rowText = 'Y';

        $line = Parsing::fixedWidth()
            ->using([
                Field::make('mapped_value', 1)->map([
                    'Y' => true,
                    'N' => false,
                ]),
            ])
            ->parseLine($rowText);

        $this->assertSame(true, $line->get('mapped_value'));

        $rowText = 'N';
        $line = Parsing::fixedWidth()
            ->using([
                Field::make('mapped_value', 1)->map([
                    'Y' => true,
                    'N' => false,
                ]),
            ])
            ->parseLine($rowText);
        $this->assertSame(false, $line->get('mapped_value'));
    }

    /** @test */
    public function it_can_explode_a_value()
    {
        $rowText = 'C12,C13,C20           ';
        $line = Parsing::fixedWidth()
            ->using([
                Field::make('codes', 20)->explode(','),
            ])
            ->parseLine($rowText);

        $this->assertSame([
            'C12', 'C13', 'C20',
        ], $line->get('codes'));
    }

    /** @test */
    public function it_can_transform_a_value()
    {
        $rowText = 'lower';
        $line = Parsing::fixedWidth()
            ->using([
                Field::make('upper', 5)->transformWith(function($value) {
                    return strtoupper($value);
                }),
            ])
            ->parseLine($rowText);

        $this->assertSame('LOWER', $line->get('upper'));
    }
    
    /** @test */
    public function it_allows_filler_fields()
    {
        $rowText = '     12345     ';

        $line = Parsing::fixedWidth()
            ->using([
                Field::filler(1, 2, 2),
                Field::make('value', 5),
                Field::filler(5),
            ])
            ->parseLine($rowText);

        $this->assertCount(1, $line->toArray());
    }
    
    /** @test */
    public function it_allows_ignored_fields()
    {
        $rowText = 'IGNOREKEEP';

        $line = Parsing::fixedWidth()
            ->using([
                Field::ignored('ignored', 6),
                Field::make('kept', 4),
            ])
            ->parseLine($rowText);

        $this->assertCount(1, $line->toArray());
        $this->assertSame([
            'kept' => 'KEEP',
        ], $line->toArray());
    }
    
    /** @test */
    public function it_parses_a_complex_string()
    {
        $rowText = '00001DOE, JOHN JOHN@DOE.COM        yblue|red            100000.00100 main street';

        $values = Parsing::fixedWidth()
            ->using([
                Field::int('id', 5),
                Field::make('name', 10),
                Field::make('email', 20),
                Field::make('active', 1)->map([
                    'y' => true,
                    'n' => false
                ]),
                Field::make('favorite_colors', 20)->explode('|'),
                Field::float('salary', 9),
                Field::make('address.uppercased', 20)->transformWith(function($value) {
                    return strtoupper($value);
                }),
            ])
            ->parseLine($rowText);

        tap($values, function($first) {
            $this->assertSame(1, $first->id);
            $this->assertSame('DOE, JOHN', $first->name);
            $this->assertSame('JOHN@DOE.COM', $first->email);
            $this->assertTrue($first->active);
            $this->assertSame([
                'blue', 'red'
            ], $first->favorite_colors);
            $this->assertSame(100000.0, $first->salary);
            $this->assertSame('100 MAIN STREET', $first->get('address.uppercased'));
        });
    }

    /** @test */
    public function it_parses_from_a_file()
    {
        $values = Parsing::fixedWidth()
            ->using([
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
            ])
            ->parse(__DIR__.'/Fixtures/fixed-width-test-file.txt')
            ->all();

        $this->assertCount(2, $values);
        tap($values[0], function($first) {
            $this->assertSame(1, $first->id);
            $this->assertSame('DOE, JOHN', $first->name);
            $this->assertSame('JOHN@DOE.COM', $first->email);
            $this->assertTrue($first->active);
            $this->assertSame([
                'blue', 'red'
            ], $first->favorite_colors);
            $this->assertSame(100000.0, $first->salary);
            $this->assertSame('100 MAIN STREET', $first->get('address.uppercased'));
        });
    }

    /** @test */
    public function it_parses_from_dedicated_definition_class()
    {
        $values = Parsing::fixedWidth()
            ->using(FixedWidthTestDefinition::class)
            ->parse(__DIR__.'/Fixtures/fixed-width-test-file.txt')
            ->all();

        $this->assertCount(2, $values);
        tap($values[0], function($first) {
            $this->assertSame(1, $first->id);
            $this->assertSame('DOE, JOHN', $first->name);
            $this->assertSame('JOHN@DOE.COM', $first->email);
            $this->assertTrue($first->active);
            $this->assertSame([
                'blue', 'red'
            ], $first->favorite_colors);
            $this->assertSame(100000.0, $first->salary);
            $this->assertSame('100 MAIN STREET', $first->get('address.uppercased'));
        });
    }

    /** @test */
    public function it_throws_an_exception_if_no_file_is_given()
    {
        try {
            $values = Parsing::fixedWidth()
                ->using(FixedWidthTestDefinition::class)
                // ->parse(__DIR__.'/Fixtures/fixed-width-test-file.txt')
                ->all();
        } catch (NoFileToParseException $e) {
            return $this->assertNotNull($e);
        }

        $this->fail('Expected exception for lack of file');
    }

    /** @test */
    public function it_throws_an_exception_if_no_definition_is_given()
    {
        try {
            $values = Parsing::fixedWidth()
                // ->using(FixedWidthTestDefinition::class)
                ->parse(__DIR__.'/Fixtures/fixed-width-test-file.txt')
                ->all();
        } catch (MissingLineDefinitionException $e) {
            return $this->assertNotNull($e);
        }

        $this->fail('Expected exception for lack of line definition');
    }
}
