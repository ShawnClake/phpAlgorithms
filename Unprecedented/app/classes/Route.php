<?php namespace App\Classes;

use App\App;
use App\Provider;
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
        'type' => null, // Possible options: plugin, module, api, page
        'response' => null,
    ];

    /**
     * Factory function for resolving a route
     * @param $uri
     * @return $this
     */
    public function makeFactory($uri)
    {
        $this->uri = $uri;

        $this->resolved = substr($uri, strlen(path_offset()) + 1);

        return $this;
    }

    /**
     * Helper function for creating the destination object from the resolved route
     * @return Route
     */
    public function destination()
    {
        // 1st Priority - Explicit plugin overrides
        foreach(App::$kernel->plugins as $plugin)
        {
            if($handler = $this->injector($plugin))
            {
                $this->destination['type'] = 'plugin';
                $this->destination['response'] = RoutingResponse::make($handler, 'plugin');
                return $this;
            }

        }

        // 2nd Priority - Explicit module overrides
        foreach(Provider::getModules() as $module)
        {
            $module = $module->instance;

            if($handler = $this->injector($module))
            {
                $this->destination['type'] = 'module';
                $this->destination['response'] = RoutingResponse::make($handler, 'module');
                return $this;
            }

        }

        // 3rd Priority - Found route in a plugins api folder
        foreach(App::$kernel->plugins as $plugin)
        {
            $reflection = new \ReflectionClass($plugin);
            $path = $reflection->getFileName();
            $pos = strrpos($path, '\\');
            if(strrpos($path, '/') > $pos)
                $pos = strrpos($path, '/');
            $apiPath = substr($path, 0, $pos + 1) . 'api';

            if(!file_exists($apiPath))
                continue;

            $routing = array_diff(scandir($apiPath), ['..', '.']);

            $resolved = str_replace('/', '.', $this->resolved);

            foreach($routing as $route)
            {
                $name = substr($reflection->name, 0, strlen($reflection->name) - 6) . 'Api\\' . substr($route, 0, strlen($route) - 4);
                if($resolved != $name::$route)
                    continue;

                $this->destination['type'] = 'api';
                $this->destination['response'] = RoutingResponse::make(new $name(), 'api');
                return $this;
            }

        }

        // 4th Priority - Must be a regular page then
        $this->destination['type'] = 'page';
        $this->destination['response'] = RoutingResponse::make(PageBuilder::make(Page::make($this->resolved)), 'page');
        return $this;
    }

    /**
     * Helper function for determining whether plugins or modules are trying to override routing
     * @param $class
     * @return bool
     */
    private function injector($class)
    {
        if(!method_exists($class, 'injectRouting'))
            return false;

        $routing = $class->injectRouting();

        $resolved = str_replace('/', '.', $this->resolved);

        if(!array_key_exists($resolved, $routing))
            return false;

        $routing = $routing[$resolved];

        if(!isset($routing['handler']))
            return false;

        $handler = $routing['handler'];

        return $class->$handler();
    }

    /**
     * Returns the response object
     * @return RoutingResponse
     */
    public function getResponse()
    {
        return $this->destination['response'];
    }

    /**
     * Returns the response type
     * @return string
     */
    public function getResponseType()
    {
        return $this->destination['type'];
    }

}