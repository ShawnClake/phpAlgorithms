<?php

namespace App\Helpers
{

    /**
     * Class Directories
     * @package App\Helpers
     */
    class Directories extends Base
    {
        /**
         * @var string[]
         */
        private $options = [
            'offset' => '/Projects/phpAlgorithms/Unprecedented',
        ];

        /**
         * Injects config entries
         */
        public function inject()
        {
            $offset = 'app/helpers';
            $base = substr(__DIR__, 0, strlen(__DIR__) - strlen($offset) - 1);
            self::$config['directories']['base'] = $base;
            self::$config['directories']['offset'] = $this->options['offset'];
        }
    }
}

/**
 * Global namespace
 */
namespace
{
    use App\Helpers\Directories;

    /**
     * @param string $suffix
     * @return mixed
     */
    function path_offset($suffix = '')
    {
        return fix_slashes(Directories::$config['directories']['offset'] . $suffix);
    }

    /**
     * Absolute path to project root
     * @param string $suffix
     * @return mixed
     */
    function path_root($suffix = '')
    {
        return fix_slashes(Directories::$config['directories']['base'] . $suffix);
    }

    /**
     * Absolute path to the app folder
     * @param string $suffix
     * @return string
     */
    function path_app($suffix = '')
    {
        return fix_slashes(path_root() . '/app' . $suffix);
    }

    /**
     * Absolute path to the config folder
     * @param string $suffix
     * @return mixed
     */
    function path_config($suffix = '')
    {
        return fix_slashes(path_root() . '/config' . $suffix);
    }

    /**
     * Absolute path to the plugins folder
     * @param string $suffix
     * @return string
     */
    function path_plugins($suffix = '')
    {
        return fix_slashes(path_root() . '/plugins' . $suffix);
    }

    /**
     * Absolute path to the themes folder
     * @param string $suffix
     * @return string
     */
    function path_themes($suffix = '')
    {
        return fix_slashes(path_root() . '/theme' . $suffix);
    }

    function path_cache($suffix = '')
    {
        return fix_slashes(path_root() . '/cache' . $suffix);
    }

    /**
     * Returns all of the files in a directory. Does so recursively.
     * BE CAREFUL WITH THIS.
     * @param $path
     * @return RecursiveIteratorIterator
     */
    function get_files_recursively($path)
    {
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(fix_slashes($path), \RecursiveDirectoryIterator::SKIP_DOTS));
    }

    /**
     * Converts an array of file contents and converts it to a string.
     * @param array $file
     * @return string
     */
    function file_to_string(array $file)
    {
        return implode(" ", $file);
    }

    /**
     * Converts a path to use dots instead of slashes
     * @param $path
     * @return mixed
     */
    function path_to_dot($path)
    {
        $path = str_replace('\\', '.', $path);
        return str_replace('/', '.', $path);
    }

    /**
     * Fixes a bug where Linux/Windows slashes don't like mixing.
     * @param $path
     * @return mixed
     */
    function fix_slashes($path)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}