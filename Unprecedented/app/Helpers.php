<?php namespace App;

/**
 * Class Helpers
 *
 * Loads the helpers for the application
 *
 * @method static Helpers expose()
 * @package App\Helpers
 */
class Helpers extends StaticFactory
{
    /**
     * Holds all of the helper config key pairs.
     * This exists to act as a sort of quasi cache. Some helper functions
     *  require expensive operations which have the same result during the same request,
     *  but vary on different requests.
     * @var array
     */
    public static $helperConfig = [];

    /**
     * Factory function which loads all of the helper files and their functions
     */
    public function exposeFactory()
    {
        $helpers = array_diff(scandir('app/helpers'), ['..', '.']);
        foreach($helpers as $helper)
        {
            if(is_file(__DIR__ . '/helpers/' . $helper))
            {
                $className = substr($helper, 0, strlen($helper) - 4);
                if($className == 'Base')
                    continue;
                $name = '\App\Helpers\\' . $className;
                self::$helperConfig[$name] = $name::config();
            }

        }

    }

    /**
     * Gets a value from an inputted key.
     * @param $helper
     * @param $key
     * @return mixed
     */
    public static function config($helper, $key)
    {
        return self::$helperConfig[$helper][$key];
    }
}