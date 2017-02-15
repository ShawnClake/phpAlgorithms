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

    function path_offset()
    {
        return Directories::$config['directories']['offset'];
    }

    function path_root()
    {
        return Directories::$config['directories']['base'] . path_offset();
    }

    function path_app()
    {
        return path_root() . '/app';
    }

    function path_plugins()
    {
        return path_root() . '/plugins';
    }
}