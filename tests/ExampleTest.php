<?php

namespace TeamZac\TextFileParsers\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\Parsing\TextFileParsersServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [TextFileParsersServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
