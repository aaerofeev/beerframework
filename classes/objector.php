<?php

class Objector
{
    protected $values = array();

    /**
     * Доступ через объект для массива
     *
     * @param array $values
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    public function __get($key)
    {
        return Arr::get($this->values, $key);
    }

    public function __set($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function __unset($key)
    {
        unset($this->values[$key]);
    }
}
