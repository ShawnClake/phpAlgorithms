<?php namespace Clake\Core;
use App\Provider;
use App\StaticFactoryTrait;

/**
 * Class Module
 *
 * The core module provider for the unprecedented framework.
 * Don't remove this module from the framework or your project under any circumstances. It will cause the framework to break.
 *
 * @package Clake\Core
 */
class Module extends Provider
{
    use StaticFactoryTrait;

    public $name = "unpCore";

    public $author = "Shawn Clake";

    public $description = "The Unprecedented Core module. This should NOT be removed from the framework under any circumstances.";

    public $version = "0.0.1";

    public function initialize() {}

    public function testReply()
    {
        echo 'Routing found in module';
    }

    public function injectRouting()
    {
        return [
            'test.test' => [
                'type' => 'GET',
                'handler' => 'testReply'
            ],
        ];
    }
}