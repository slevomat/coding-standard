<?php

class Foo
{

	public function classMethod()
	{
		function nonClassMethodFunction($value) {
			return $value;
		}
		return nonClassMethodFunction('foo');
	}

}
