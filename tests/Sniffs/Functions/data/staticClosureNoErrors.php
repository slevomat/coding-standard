<?php // lint >= 7.4

static function ()
{

};

static fn ($a) => $a;

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

	public function withThisInString()
	{
		return array_map(function ($i) {
			return "{$this->anything}{$i}";
		}, []);
	}

	public function arrayFilterEtc($list)
	{
		return count(array_filter($list->call('something'), fn (object $object): bool => $object->getAnything() === $this->anything)) === 1;
	}

}

Closure::bind(function ($instance, $value) {
	$instance->property = $value;
}, $instance, $instance);
