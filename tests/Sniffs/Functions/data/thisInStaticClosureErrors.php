<?php // lint >= 7.4

static function ()
{

};

static fn ($a) => $a;

class Whatever extends Something
{

	private $anything = 1;

	public function staticWithThis()
	{
		return array_map(static function ($i) {
			return $this->anything;
		}, []);
	}

	public function staticWithThisInString()
	{
		return array_map(static function ($i) {
			return "{$this->anything}:{$i}";
		}, []);
	}

	public function arrowFnStaticWithThis()
	{
		return array_map(static fn ($i) => "{$this->anything}:{$i}", []);
	}
}
