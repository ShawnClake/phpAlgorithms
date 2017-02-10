<?php
require_once('UserExtended.php');
require_once('classb.php');
require_once('classc.php');
require_once('classd.php');
require_once('classa.php');
require_once('classe.php');
class pluginTest
{
	
	public function __construct()
	{
		$this->register();
		$this->boot();
	}
	
	public function register()
	{
		ClassB::register();
		//$classc = new ClassC;
		ClassC::register(); // Needs to have the trait StaticFactoryTrait in order to be registered this way
		
		ClassA::register();
		
		ClassE::register();
	}
	
	public function boot()
	{
		UserExtended::boot();
		
		echo json_encode(UserExtended::getComponents()) . '<br><br>';
		
		UserExtended::dumpModules();
		
		echo '<br><br>' . UserExtended::shawnPickler()->getVersion() . '<br><br>';
		
		echo json_encode(UserExtended::isModuleLoaded('shawnPickler')) . '<br><br>';
		
		$this->randomStuff();
	}
	
	public function randomStuff()
	{
		echo(UserExtended::shawnPickler()->testMe());
		echo '  ';
		echo(UserExtended::Cheese()->testMe());

		echo '<br><br><br>';

		$classd = ClassD::destroy('never say never ')->appendAndPrint('not like this');
	}
	
}

$test = new pluginTest();