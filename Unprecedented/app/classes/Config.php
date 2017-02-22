<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class Config
 * @method static Config load()
 * @package App\Classes
 */
class Config extends StaticFactory
{
    /** @var array */
    public static $config = [];

    public function loadFactory()
    {
        if(!file_exists(path_config()))
            return $this;

        $files = get_files_recursively(path_config());

        foreach($files as $config)
        {
            $len = strlen(path_config()) + 1;
            $key = strtolower(substr($config, $len, strlen($config) - $len - 4));
            $pairs = include fix_slashes($config);
            self::$config[$key] = $pairs;
        }
        return $this;
    }

    public function get($config, $key)
    {
        if(!key_exists($config, self::$config))
            return false;

        if(!key_exists($key, self::$config[$config]))
            return false;

        echo 'test';
    }
}