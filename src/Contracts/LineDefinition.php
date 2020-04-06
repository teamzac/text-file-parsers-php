<?php

namespace TeamZac\Parsing\Contracts;

interface LineDefinition
{
    /** 
     * Return an array of Field objects that defines how the string should be parsed
     *
     * @return Field[]
     */
    public function fieldDefinitions(): array;
}