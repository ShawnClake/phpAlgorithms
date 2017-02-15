<?php namespace App;

use \App\Classes\Route;

/**
 * Class Kernel
 * @method static Kernel init()
 * @package App
 */
class Kernel extends StaticFactory
{
    public $plugins;

    public function initFactory()
    {
        $this->generatePluginListing();
        return $this;
    }

    public function generatePluginListing()
    {
        $absolutePath = __DIR__ . '/../plugins/';

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

    public function register()
    {
        foreach($this->plugins as $plugin)
        {
            $plugin->register();
        }

    }

    public function boot()
    {
        foreach($this->plugins as $plugin)
        {
            $plugin->boot();
        }
        Provider::boot();
    }

    public function route()
    {
        $uri = $this->getUri();

        $route = Route::make($uri);
    }

    private function getUri()
    {
        $uri = urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );

        return $uri;
    }

}