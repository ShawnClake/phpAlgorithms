<?php namespace App\Drivers;

use App\Drivers\Cache\iCache;

/**
 * Class Cache
 * @method static Cache boot()
 * @package App\Drivers
 */
abstract class Cache implements iCache
{
    public function test()
    {
        echo 'Cache Driver On';
    }

    public function __construct()
    {
        $this->initCache();
    }

    private function initCache()
    {
        if(!file_exists(path_cache()))
            mkdir(path_cache());
    }
}