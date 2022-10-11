<?php // lint >= 7.4

static function ()
{

};

static fn ($a) => $a;

class Whatever extends Something
{

	private $anything = 1;

	public function nonStaticWithThis()
	{
		return array_map(function ($i) {
			return preg_replace_callback('(.*)', function (array $matches) {
				return (int) $matches[1] . $this->anything;
			}, $i);
		}, []);
	}

	public function staticWithoutThis()
	{
		return array_map(static function ($i) {
			return ":{$i}";
		}, []);
	}
}
