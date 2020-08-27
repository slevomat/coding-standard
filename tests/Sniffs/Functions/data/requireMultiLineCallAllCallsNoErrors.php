<?php // lint >= 7.4

class Whatever
{
	public function foo()
	{
		$this->doSomething();

		return new self();
	}
}

gc_disable();
