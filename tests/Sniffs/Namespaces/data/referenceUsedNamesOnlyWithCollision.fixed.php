<?php

class Foo
{
	public function throwNamespacedException()
	{
		throw new \Some\Namespaced\Exception();
	}

	public function throwGenericException()
	{
		throw new Exception();
	}
}
