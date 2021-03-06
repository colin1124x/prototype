<?php namespace Rde;

class Prototype implements \ArrayAccess
{
    private $drivers = array();

    final public function extend($name, $callable)
    {
        $this->drivers[$name] = $callable;

        return $this;
    }

    final public function hasDriver($name)
    {
        return isset($this->drivers[$name]);
    }

    final public function __call($name, $args)
    {
        if ($this->hasDriver($name)) {
            array_unshift($args, $this);

            return \Rde\call($this->drivers[$name], $args);
        }

        throw new \BadMethodCallException("沒有安裝[{$name}]處理驅動");
    }

    public function __toString()
    {
        if ($this->hasDriver('__toString')) {
            return $this->__call('__toString', array());
        }

        return '['.get_called_class().']';
    }

    final public function offsetExists($key)
    {
        return isset($this->drivers[$key]);
    }

    final public function offsetGet($key)
    {
        return $this->drivers[$key];
    }

    final public function offsetSet($key, $val)
    {
        $this->extend($key, $val);
    }

    final public function offsetUnset($key)
    {
        unset($this->drivers[$key]);
    }
}
