<?php namespace Clake\Core;
use App\Provider;
use App\StaticFactoryTrait;

class Module extends Provider
{
    use StaticFactoryTrait;

    public $name = "Cheese";

    public $author = "Shawn";

    public $description = "Hi whats up";

    public $version = "0.0.1";

    public function testMe()
    {
        return 'hell no';
    }

    public function returnData()
    {
        return ['this isnt data' => 'or is it?'];
    }

    public function initialize() {}
}