<?php

namespace App\Helpers
{
    class Directories extends Base
    {
        private $options = [
            'offset' => '/Projects/phpAlgorithms/Unprecedented',
        ];

        public function inject()
        {
            $offset = 'app/helpers';
            $base = substr(__DIR__, 0, strlen(__DIR__) - strlen($offset) - 1);
            self::$config['directories']['base'] = $base;
            self::$config['directories']['offset'] = $this->options['offset'];
        }
    }
}

namespace
{
    use App\Helpers\Directories;

    function path_offset($suffix = '')
    {
        return Directories::$config['directories']['offset'] . $suffix;
    }

    function path_root($suffix = '')
    {
        return Directories::$config['directories']['base'] . $suffix;
    }

    function path_app($suffix = '')
    {
        return path_root() . '/app' . $suffix;
    }

    function path_plugins($suffix = '')
    {
        return path_root() . '/plugins' . $suffix;
    }

    function path_themes($suffix = '')
    {
        return path_root() . '/theme' . $suffix;
    }

    function get_files_recursively($path)
    {
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS));
    }
}