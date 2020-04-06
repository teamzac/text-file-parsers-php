<?php

namespace TeamZac\Parsing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TeamZac\Parsing\Parser
 */
class Parsing extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'text-file-parsers';
    }
}
