<?php namespace App\Traits;

/**
 * Class MagicGetSet
 * @package App\Traits
 */
trait MagicGetSet
{
    /** @var string[] */
    private $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if(array_key_exists($name, $this->data))
            return $this->data[$name];

        return null;
    }
}