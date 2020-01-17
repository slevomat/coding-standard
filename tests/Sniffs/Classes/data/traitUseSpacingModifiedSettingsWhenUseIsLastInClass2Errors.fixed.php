<?php

namespace A;

class TestClass
{
	public function __construct()
	{
	}
	use SomeTrait {
		methodName as overridenName;
	}
}
