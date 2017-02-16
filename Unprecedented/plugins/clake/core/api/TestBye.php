<?php namespace Clake\Core\Api;

class TestBye
{
    public static $route = 'test.bye';

    public function __construct()
    {
        echo 'Test.Bye Route Constructor Found';
    }
}