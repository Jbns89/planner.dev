<?php

abstract class Model
{
    protected $dbc;

    protected $id;

    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    public function __get($key)
    {
        if ($key == 'dbc') {
            throw new LogicException('Get your own $dbc!');
        }

        return htmlentities($this->$key, ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE, 'UTF-8', false);
    }

    public function __set($key, $value)
    {
        if ($key == 'dbc') {
            throw new LogicException('Cannot modify DBC property!');
        }

        $this->$key = trim($value);
    }

    public function __isset($key)
    {
        return isset($this->$key);
    }

    public function save()
    {
        if (isset($this->id)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    protected function validate($value, $name, $max)
    {
        if (empty($value)) {
            throw new Exception("$name is required.");
        } elseif (strlen($value) > $max) {
            throw new Exception("$name cannot be longer than $max characters.");
        }
    }

    protected abstract function update();
    protected abstract function insert();
    public abstract function delete();
}
