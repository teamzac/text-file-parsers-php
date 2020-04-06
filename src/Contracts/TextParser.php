<?php

namespace TeamZac\Parsing\Contracts;

use TeamZac\Parsing\Support\ParsedLine;

interface TextParser
{
    /**
     * Get all records in one swoop. If your file isn't
     * too large, you might choose to use this method
     * instead of iterating one at a time.
     *
     * @return ParsedLine[]
    */
	public function all();

    /** 
     * Parse the file and return each record one at a time.
     * Definitely use this for larger files instead of all()
     *
     * @param   Callable $callback
     */
	public function each($callback);

	/**
	 * Parse the given text string and return a ParsedLine
	 * based on the line definition we have been given.
	 *
	 * @param string $value
	 * @return ParsedLine
	 */
	public function parseLine($value): ParsedLine;
}