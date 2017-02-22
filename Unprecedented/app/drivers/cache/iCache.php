<?php namespace App\Drivers\Cache;

interface iCache
{
    public function test();

    public function bootFactory();

    public function isCached($offset, $filename, $content);

    public function store($offset, $filename, $content, $md5 = '');

    public function retrieve($offset, $filename);
}