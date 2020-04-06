<?php

namespace TeamZac\Parsing\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class MissingLineDefinitionException extends Exception implements ProvidesSolution 
{
	public static function throw()
	{
        throw new static('You must provide a line definition with the using() method before parsing can occur.');
	}

    public function getSolution(): Solution
    {
        return BaseSolution::create('Provide a line definition')
            ->setSolutionDescription('You can either provide a dedication class that implements the TeamZac\Parsing\Contracts\LineDefinition interface, or provide an array of Field objects.');
    }
}
