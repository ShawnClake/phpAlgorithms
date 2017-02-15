<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class Route
 * @method static Route make($uri)
 * @package App\Classes
 */
class Route extends StaticFactory
{
    private $uri;

    private $resolved;

    private $destination = [
      'type' => null // Possible options: api, page
    ];

    public function makeFactory($uri)
    {
        $this->uri = $uri;

        $this->resolved = substr($uri, strlen(path_offset()));
    }

    public function destination()
    {

    }

}