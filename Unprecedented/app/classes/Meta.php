<?php namespace App\Classes;

use App\Traits\MagicGetSet;

/**
 * Class Meta
 *
 * Generic meta data object.
 * TODO: Use magic methods for setting and getting meta data.
 *
 * @package App\Classes
 */
class Meta
{
    use MagicGetSet;

    /**
     * Returns all of the meta data
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }
}