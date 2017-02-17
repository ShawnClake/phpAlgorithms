<?php namespace App;

use App\Theme\Theme;
use App\Theme\ThemeBase;

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
     * @var Kernel
     */
    public static $kernel;

    /**
     * Helpers
     * @var Helpers
     */
    public static $helpers;

    /**
     * Autoloader
     * @var Autoloader
     */
    public static $autoloader;

    /**
     * @var ThemeBase
     */
    public static $theme;

    /**
     * Factory function which handles adding in helpers and starting up the kernel
     * @return $this
     */
    public function makeFactory()
    {
        $this->exposeHelpers();
        $this->kernelStartup();
        $this->themeStartup();
        return $this;
    }

    /**
     * Helper function for getting the selected theme and starting it up
     */
    public function themeStartup()
    {
        self::$theme = Theme::getSelectedTheme();
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
        self::$kernel->route();
        self::$kernel->response();
    }

}