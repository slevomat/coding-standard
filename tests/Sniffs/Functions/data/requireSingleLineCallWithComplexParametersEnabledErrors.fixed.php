<?php // lint >= 7.4

class Whatever
{
	public function foo()
	{
		self::doSomething(array_map(function () {return true;}, []));

		$this->doAnything(fn () => true);

		return new self([true, false]);

		self::doWhatever(self::doAnything(true, false));
	}
}
