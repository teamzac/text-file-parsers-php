<?php

namespace TeamZac\Parsing\FixedWidth;

use Carbon\Carbon;
use Illuminate\Support\Arr;

class Field
{
    /** 
     * The key used to access this field
     *
     * @var string 
    */
    protected $key;

    /**
     * The number of characters for this field
     *
     * @var int
     */
    protected $length;

    /**
     * Casting strategy for this value. Set using asString(), asInt(), etc
     *
     * @var string
     */
    protected $castAs = 'string';

    /**
     * Format string for parsing as a date
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * Should the value parsed from this field be included or ignored?
     *
     * @var bool
     */
    protected $ignored = false;

    /** 
     * Should the value that we found be trimmed or left alone?
     *
     * @var bool
     */
    protected $trimmed = true;

    /**
     * This can be used to map values parsed from the source
     * to some other value that may be more useful to you
     *
     * ex: ['T' => true, 'F' => false]
     * @var array
     */
    protected $valueMap = [];

    /**
     * A callback function that can be used to give more fine-grained 
     * control over how the extracted value is transformed
     *
     * @var Callable|null
     */
    protected $transformCallback;

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
     * Get the key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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

    /**
     * Cast this value as a string
     */
    public function asString()
    {
        return $this->as('string');
    }

    /**
     * Cast this value as an integer
     */
    public function asInt()
    {
        return $this->as('int');
    }

    /**
     * Cast this value as a float
     */
    public function asFloat()
    {
        return $this->as('float');
    }

    /**
     * Cast this value as a bool
     */
    public function asBool()
    {
        return $this->as('bool');
    }

    /**
     * Cast this value as a date
     */
    public function asDate($format = 'Y-m-d')
    {
        return $this->setDateFormat($format)
            ->as('date');
    }

    /**
     * Set the date format
     *
     * @param string $format
     * @return $this
     */
    public function setDateFormat($format = 'Y-m-d')
    {
        $this->dateFormat = $format;
        return $this;
    }

    /** 
     * Set the $valueMap property
     *
     * @var array
     * @return $this
     */
    public function map(array $values)
    {
        $this->valueMap = $values;
        return $this;
    }

    /** 
     * Denote that this field should be ignored
     *
     * @return $this
     */
    public function ignore()
    {
        $this->ignored = true;
        return $this;
    }

    /** 
     * Denote that this field should be trimmed (the default)
     *
     * @return $this
     */
    public function trimmed()
    {
        $this->trimmed = true;
        return $this;
    }

    /** 
     * Denote that this field should NOT be trimmed
     *
     * @return $this
     */
    public function untrimmed()
    {
        $this->trimmed = false;
        return $this;
    }

    /** 
     * Set the transform callback, which receives the raw value
     *
     * @var Callable $callback
     * @return $this
     */
    public function transformWith($callback)
    {
        $this->transformCallback = $callback;
        return $this;
    }

    /**
     * A convenient way to create a transformation callback
     * that simply explodes the raw value on the given delimiter
     *
     * @var string $deimiter
     */
    public function explode($delimiter = ',')
    {
        return $this->transformWith(function($value) use ($delimiter) {
            return explode($delimiter, $value);
        });
    }

    /** 
     * Should this field be included?
     *
     * @return bool
     */
    public function shouldBeIncluded()
    {
        return ! $this->ignored;
    }

    /**
     * Cast the raw value 
     *
     * @var string $value
     * @return mixed
     */
    public function getCastedValue($value)
    {
        $value = $this->trimmed ? trim($value) : $value;

        if ($this->transformCallback) {
            $callback = $this->transformCallback;
            return $callback($value);
        }

        if (count($this->valueMap)) {
            $value = Arr::get($this->valueMap, $value);
        }

        if ($this->castAs == 'int') {
            return (int) $value;
        } else if ($this->castAs == 'float') {
            return (float) $value;
        } else if ($this->castAs == 'bool') {
            return (bool) $value;
        } else if ($this->castAs == 'date') {
            return Carbon::createFromFormat($this->dateFormat, $value);
        }

        return $value;
    }

    /**
     * Set the $castAs property
     *
     * @var string $cast
     * @return $this
     */
    protected function as($cast)
    {
        $this->castAs = $cast;
        return $this;
    }
}
