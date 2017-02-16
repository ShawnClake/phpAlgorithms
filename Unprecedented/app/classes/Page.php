<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class Page
 * @method static Page make($routing)
 * @package App\Classes
 */
class Page extends StaticFactory
{
    public $routing;

    public function makeFactory($routing)
    {
        $this->routing = $routing;
        return $this;
    }

}