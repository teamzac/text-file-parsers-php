<?php

namespace TeamZac\Parsing\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use TeamZac\Parsing\Delimited\Field;
use TeamZac\Parsing\Exceptions\MissingLineDefinitionException;
use TeamZac\Parsing\Exceptions\NoFileToParseException;
use TeamZac\Parsing\Facades\Parsing;
use TeamZac\Parsing\Tests\Fixtures\DelimitedTestDefinition;
use TeamZac\Parsing\Tests\Fixtures\FixedWidthTestDefinition;
use TeamZac\Parsing\TextFileParsersServiceProvider;

class DelimitedParsingTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TextFileParsersServiceProvider::class];
    }
    /** @test **/
    public function it_parses_csv_by_default()
    {
        $line = 'john,doe,john@example.com';

        $values = Parsing::delimited()
            ->using([
                Field::make('first_name'),
                Field::make('last_name'),
                Field::make('email'),
            ])
            ->parseLine($line);

        $this->assertSame('john', $values->first_name);
        $this->assertSame('doe', $values->last_name);
        $this->assertSame('john@example.com', $values->email);
    }

    /** @test */
    public function it_can_use_a_custom_delimeter_value()
    {
        $line = 'john|doe|john@example.com';

        $values = Parsing::delimited('|')
            ->using([
                Field::make('first_name'),
                Field::make('last_name'),
                Field::make('email'),
            ])
            ->parseLine($line);

        $this->assertSame('john', $values->first_name);
        $this->assertSame('doe', $values->last_name);
        $this->assertSame('john@example.com', $values->email);
    }

    /** @test */
    public function it_can_use_a_custom_definition_class()
    {
        $values = Parsing::delimited()
            ->using(DelimitedTestDefinition::class)
            ->parse(__DIR__.'/Fixtures/delimited-test-file.txt')
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
            $values = Parsing::delimited()
                ->using(DelimitedTestDefinition::class)
                // ->parse(__DIR__.'/Fixtures/delimited-test-file.txt')
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
            $values = Parsing::delimited()
                // ->using(DelimitedTestDefinition::class)
                ->parse(__DIR__.'/Fixtures/delimited-test-file.txt')
                ->all();
        } catch (MissingLineDefinitionException $e) {
            return $this->assertNotNull($e);
        }

        $this->fail('Expected exception for lack of line definition');
    }
}
