<?php

class Whatever
{

	const FOO = 'foo';
	const BOO = [
		'boo',
	];

	public function doSomething()
	{
		echo self::FOO . self::BOO[0];
	}

}
