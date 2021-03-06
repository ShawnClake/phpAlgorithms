<?php namespace App;

use \App\Classes\Route;
use App\Drivers\Cache;
use App\Drivers\Database;
use App\Drivers\Socket;

/**
 * Class Kernel
 *
 * The Kernel for the Unprecedented framework
 *
 * @method static Kernel init()
 * @package App
 */
class Kernel extends StaticFactory
{
    /**
     * Array of all the registered plugins
     * @var array
     */
    public $plugins;

    /**
     * Current requested route object
     * @var Route
     */
    public $route;

    /**
     * Socket Driver
     * @var Socket
     */
    public $socket = null;

    /**
     * Database Driver
     * @var Database
     */
    public $db = null;

    /**
     * Cache Driver
     * @var Cache
     */
    public $cache = null;

    /**
     * Factory function which:
     *  Generates a plugin listing.
     *  Initializes application drivers
     * @return $this
     */
    public function initFactory()
    {
        $this->generatePluginListing();
        $this->loadDrivers();
        return $this;
    }

    /**
     * Helper function which parses the project to setup a plugin listing
     */
    private function generatePluginListing()
    {
        $absolutePath = path_plugins('/');

        $authors = array_diff(scandir('plugins'), ['..', '.']);
        foreach($authors as $author)
        {
            if(is_dir($absolutePath . $author))
            {
                $plugins = array_diff(scandir('plugins/' . $author), ['..', '.']);
                foreach($plugins as $plugin)
                {
                    if (is_dir($absolutePath . $author . '/' . $plugin))
                    {
                        $name = ucfirst($author) . '\\' . ucfirst($plugin) . '\\' . 'Plugin';
                        $pluginInstance = new $name();
                        $this->plugins[] = $pluginInstance;
                    }
                }
            }
        }
    }

    /**
     * Preforms the registration operation on the project.
     *  Plugins
     *  Modules
     */
    public function register()
    {
        foreach($this->plugins as $plugin)
        {
            $plugin->register();
        }

    }

    /**
     * Preforms the boot operation on the project.
     *  Plugins
     */
    public function boot()
    {
        foreach($this->plugins as $plugin)
        {
            $plugin->boot();
        }

        Provider::boot();
    }

    /**
     * Creates a route object for the current request
     */
    public function route()
    {
        $uri = $this->getUri();

        $route = Route::make($uri)->destination();
        $this->route = $route;
    }

    public function response()
    {
        $this->route->getResponse()->render();
    }

    /**
     * Loads the drivers selected in the config
     */
    public function loadDrivers()
    {
        /** @var Cache $cache */
        if($cache = App::$config->get('cache', 'driver'))
        {
            $cache = 'App\\Drivers\\Cache\\' . $cache;
            if(class_exists($cache))
                $this->cache = $cache::boot();
        }




    }

    /**
     * Helper object for grabbing the current uri
     * @return string
     */
    private function getUri()
    {
        $uri = urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );

        return $uri;
    }

}