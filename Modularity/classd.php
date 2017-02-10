<?php
require_once('StaticFactory.php');
class ClassD extends StaticFactory
{
	public $randomThing = '';
	
	public function destroyFactory($injected = 'I am an injected variable after being statically created')
	{
		$this->randomThing = $injected;
		return $this;
	}
	
	public function appendAndPrint($appended = ' nononoono')
	{
		$this->randomThing .= $appended;
		echo $this->randomThing;
	}
	
}