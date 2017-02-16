<?php namespace App;

/**
 * Class App
 *
 * The basis of the framework. The application class handles the flow control.
 * Think of it as one big delegater.
 *
 * @method static App make()
 * @package App
 */
class App extends StaticFactory
{

    /**
     * Kernel
     * @var \App\Kernel
     */
    public static $kernel;

    /**
     * Helpers
     * @var
     */
    public static $helpers;

    /**
     * Autoloader
     * @var
     */
    public static $autoloader;

    /**
     * Factory function which handles adding in helpers and starting up the kernel
     * @return $this
     */
    public function makeFactory()
    {
        $this->exposeHelpers();
        $this->kernelStartup();
        return $this;
    }

    /**
     * Helper function for exposing the helper function to the global scope
     */
    public function exposeHelpers()
    {
        self::$helpers = Helpers::expose();
    }

    /**
     * Helper function which delegates the startup of the Kernel
     */
    public function kernelStartup()
    {
        self::$kernel = Kernel::init();
        self::$kernel->register();
        self::$kernel->boot();
    }

    /**
     * Handles the routing of a request
     */
    public function route()
    {
        $route = self::$kernel->route();
    }

}