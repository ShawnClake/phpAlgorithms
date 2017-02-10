<?php
require_once('UserExtended.php');
class ClassC extends UserExtended
{
	use StaticFactoryTrait;

	public $name = "shawnPickler";
	
	public $author = "Shawn Clake";

    public $description = "I cant";
	
	public $version = "0.1.2";
	
	//public $visible = false;
	
	public function injectComponents()
	{
		return [];
		//return ['burger\ClassA' => 'test1'];
	}
	
	public function injectNavigation()
	{
		return [];
	}
	
	public function injectLang()
	{
		return [];
	}
	
	public function initialize() {
		if(UserExtended::isModuleLoaded('Cheese'))
			//echo 'hi';
			echo json_encode(UserExtended::Cheese()->returnData()) . '<br><br>';
	}
	
	public function testMe()
	{
		return 'Is this test good enough?';
	}
	
}