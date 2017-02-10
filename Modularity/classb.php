<?php
require_once('UserExtended.php');
class ClassB extends UserExtended
{
	use StaticFactoryTrait;
	
	public $name = "Cheese";
	
	public $author = "Shawn";

    public $description = "Hi whats up";
	
	public $version = "0.0.1";
	
	public function injectComponents()
	{
		return [];
		//return ['ClassA' => 'test1'];
	}
	
	public function injectNavigation()
	{
		return [];
	}
	
	public function injectLang()
	{
		return [];
	}
	
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