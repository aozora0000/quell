<?php

namespace Querial\Helper;

use Querial\Exceptions\InvalidEnumerationException;
use ReflectionObject;

abstract class Enumeration
{
    private $scalar;

    public function __construct($value)
    {
        $ref = new ReflectionObject($this);
        $consts = $ref->getConstants();
        if (!in_array($value, $consts, true)) {
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

    //元の値を取り出すメソッド。
    //メソッド名は好みのものに変更どうぞ
    final public function of()
    {
        return $this->scalar;
    }

    final public function __toString()
    {
        return (string)$this->scalar;
    }
}