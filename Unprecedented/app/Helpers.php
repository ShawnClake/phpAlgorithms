<?php namespace App;

/**
 * Class Helpers
 * @method static Helpers expose()
 * @package App\Helpers
 */
class Helpers extends StaticFactory
{
    public static $helperConfig = [];

    public function exposeFactory()
    {
        $helpers = array_diff(scandir('app/helpers'), ['..', '.']);
        foreach($helpers as $helper)
        {
            if(is_file(__DIR__ . '/helpers/' . $helper))
            {
                $className = substr($helper, 0, strlen($helper) - 4);
                $name = 'App\Helpers\\' . $className;
                self::$helperConfig[$name] = $name::config();
            }

        }

    }

    public static function config($helper, $key)
    {
        return self::$helperConfig[$helper][$key];
    }
}