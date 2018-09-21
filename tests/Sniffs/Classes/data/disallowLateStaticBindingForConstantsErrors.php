<?php

class Whatever
{

	const FOO = 'foo';
	const BOO = [
		'boo',
	];

	public function doSomething()
	{
		echo static::FOO . static::BOO[0];
	}

}
