<?php

use Nette\ObjectPrototype;
class Bar extends \ObjectPrototype implements \Iterator
{

	public function bar()
	{
		new ObjectPrototype();
	}

}
