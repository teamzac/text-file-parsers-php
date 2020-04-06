<?php

namespace TeamZac\Parsing\FixedWidth;

use Illuminate\Support\Arr;
use TeamZac\Parsing\Support\BaseField;

class Field extends BaseField
{
    /**
     * The number of characters for this field
     *
     * @var int
     */
    protected $length;

    /**
     * Static constructor, convenient for chaining additional constraints
     *
     * @param   string $key
     * @param   int $length
     */
    public static function make($key, $length)
    {
        return new static($key, $length);
    }

    /**
     * Quickly create an int field
     */
    public static function int($key, $length)
    {
        return static::make($key, $length)->asInt();
    }

    /**
     * Quickly create a float field
     */
    public static function float($key, $length)
    {
        return static::make($key, $length)->asFloat();
    }

    /**
     * Quickly create a bool field
     */
    public static function bool($key, $length)
    {
        return static::make($key, $length)->asBool();
    }

    /**
     * Quickly create a date field
     */
    public static function date($key, $length, $format = 'Y-m-d')
    {
        return static::make($key, $length)->asDate($format);
    }

    /**
     * You can use this to specify a filler field, which is either 
     * a true filler field, or perhaps just something you care so 
     * little about that you don't even bother to name it
     *
     * If your source file has consecutive filler fields, you can simply
     * add the lengths up yourself, or use a variadic argument
     *
     * @param   int $length
     */
    public static function filler(...$length)
    {
        $length = Arr::wrap($length);
        return static::make('filler', array_sum($length))->ignore();
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
    public static function ignored($key, $length)
    {
        return static::make('ignored', $length)->ignore();
    }

    /**
     * @param   string $key
     * @param   string $length
     */
    public function __construct($key, $length)
    {
        $this->key = $key;
        $this->length = $length;
    }

    /** 
     * Get the length
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}
