<?php

namespace TeamZac\Parsing\Support;

use Illuminate\Support\Arr;

class ParsedLine
{
    /** @var array */
    protected $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Return the attributes
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Provide a convenient way to access the attributes
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * If you need to access a nested property, you can use
     * this method instead of the magic method above
     */
    public function get($key)
    {
        return Arr::get($this->attributes, $key);
    }
}
