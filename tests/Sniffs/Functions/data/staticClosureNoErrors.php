<?php

static function ()
{

};

class Whatever
{

	private $anything = 1;

	public function doSomething()
	{
		return array_map(function ($i) {
			return preg_replace_callback('(.*)', function (array $matches) {
				return (int) $matches[1] . $this->anything;
			}, $i);
		}, []);
	}

}
