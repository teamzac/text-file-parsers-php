<?php

namespace TeamZac\Parsing\Delimited;

use TeamZac\Parsing\Support\TextField;

class Field extends TextField
{
    /**
     * Static constructor, convenient for chaining additional constraints
     *
     * @param   string $key
     * @param   int $length
     */
    public static function make($key)
    {
        return new static($key);
    }

    /**
     * Quickly create an int field
     */
    public static function int($key)
    {
        return static::make($key)->asInt();
    }

    /**
     * Quickly create a float field
     */
    public static function float($key)
    {
        return static::make($key)->asFloat();
    }

    /**
     * Quickly create a bool field
     */
    public static function bool($key)
    {
        return static::make($key)->asBool();
    }

    /**
     * Quickly create a date field
     */
    public static function date($key, $format = 'Y-m-d')
    {
        return static::make($key)->asDate($format);
    }

    /**
     * You can use this to quickly specify a field that you wish to ignore when
     * parsing the file. The value will not be included in the final results.
     * 
     * Using 'ignored()' maybe helpful for you to keep track of where you are
     * in the source file, since you still provide a key upon creation.
     *
     * @param   int $length
     */
    public static function ignored($key)
    {
        return static::make('ignored')->ignore();
    }

    /**
     * @param   string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }
}