<?php

namespace TeamZac\Parsing\Support;

use Carbon\Carbon;
use Illuminate\Support\Arr;

class BaseField
{
    /** 
     * The key used to access this field
     *
     * @var string 
    */
    protected $key;

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
     * Get the key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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
     *
     * @param string $format
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
     * @param array
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
     * @param Callable $callback
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
     * @param string $deimiter
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
     * @param string $value
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
            return $this->castToDate($value);
        }

        return $value;
    }

    /** 
     * Cast the value to a date based on the default format
     * If the value is empty, return a null response
     *
     * @param string $value
     * @return Carbon|null
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    protected function castToDate($value)
    {
        if ($value === '') {
            return null;
        }

        return Carbon::createFromFormat($this->dateFormat, $value);
    }

    /**
     * Set the $castAs property
     *
     * @param string $cast
     * @return $this
     */
    protected function as($cast)
    {
        $this->castAs = $cast;
        return $this;
    }
}
