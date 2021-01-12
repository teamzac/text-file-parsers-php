<?php

namespace TeamZac\Parsing\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class NoFileToParseException extends Exception implements ProvidesSolution 
{
	public static function throw()
	{
        throw new static('You must provide a valid file path to the parse() method.');
	}

    public function getSolution(): Solution
    {
        return BaseSolution::create('Provide a file path')
            ->setSolutionDescription('Pass a valid file path to the parse() method of your file parser.');
    }
}
