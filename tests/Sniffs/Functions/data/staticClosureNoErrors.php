<?php

static function ()
{

};

class Whatever extends Something
{

	private $anything = 1;

	public function withThis()
	{
		return array_map(function ($i) {
			return preg_replace_callback('(.*)', function (array $matches) {
				return (int) $matches[1] . $this->anything;
			}, $i);
		}, []);
	}

	public function withParent()
	{
		return array_map(function ($i) {
			return preg_replace_callback('(.*)', function (array $matches) {
				return (int) $matches[1] . parent::doSomething();
			}, $i);
		}, []);
	}

}
