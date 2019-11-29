<?php // lint >= 7.4

static function ()
{

};

static fn ($a) => $a;

class Whatever
{

	public function doSomething()
	{
		return array_map(static function ($i) {
			return preg_replace_callback('(.*)', static function (array $matches) {
				return $matches[1];
			}, $i);
		}, []);
	}

}
