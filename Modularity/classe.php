<?php
require_once('UserExtended.php');
class ClassE extends UserExtended
{
	use StaticFactoryTrait;
	
	public $name = "srth";
	
	public $author = "bob robert";

    public $description = "i cant even";
	
	public $version = "0.59.1";
	
	public function initialize() {}
	
	public function injectComponents()
	{
		return [];
		//return ['test\ClassA' => 'test1'];
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
		return 'sigh';
	}
	
}