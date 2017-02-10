<?php
require_once('UserExtended.php');
class ClassA extends UserExtended
{
	use StaticFactoryTrait;
	
	public $name = "Naw";
	
	public $author = "Burger Man";

    public $description = "Not much";
	
	public $version = "0.0.90";
	
	public function injectComponents()
	{
		return [];
		//return ['bob/ClassA' => 'test1'];
	}
	
	public function initialize() {}
	
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
		return 'yes bye';
	}
	
}