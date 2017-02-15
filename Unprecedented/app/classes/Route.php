<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class Route
 * @method static Route make($uri)
 * @package App\Classes
 */
class Route extends StaticFactory
{
    /**
     * Requested URI
     * @var
     */
    private $uri;

    /**
     * Resolved Route
     * @var
     */
    private $resolved;

    /**
     * The resolved route's destination.
     * Holds information about the destination such as:
     *  Fully qualified class name
     *  Type
     *  Meta data about where the route came from
     * @var array
     */
    private $destination = [
      'type' => null // Possible options: plugin, module, api, page
    ];

    /**
     * Factory function for resolving a route
     * @param $uri
     */
    public function makeFactory($uri)
    {
        $this->uri = $uri;

        $this->resolved = substr($uri, strlen(path_offset()));
    }

    /**
     * Helper function for creating the destination object from the resolved route
     */
    public function destination()
    {
        // 1st Priority - Explicit plugin overrides
        // 2nd Priority - Explicit module overrides
        // 3rd Priority - Found route in a plugins api folder
        // 4th Priority - Must be a regular page then
    }

}