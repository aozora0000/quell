<?php

namespace Querial\Contracts;

use Querial\Exceptions\InvalidEnumerationException;
use ReflectionObject;

abstract class Enumeration
{
    protected $scalar;

    public function __construct($value)
    {
        $ref = new ReflectionObject($this);
        if (!in_array($value, $ref->getConstants(), true)) {
            throw new InvalidEnumerationException($value . ' is not found enumeration');
        }

        $this->scalar = $value;
    }

    final public static function __callStatic($label, $args)
    {
        $class = get_called_class();
        $const = constant("$class::$label");
        return new $class($const);
    }

    public function of()
    {
        return $this->scalar;
    }

    final public function __toString()
    {
        return (string)$this->scalar;
    }
}