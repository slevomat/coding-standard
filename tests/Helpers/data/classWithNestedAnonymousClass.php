<?php

class Foo
{

	public function classMethod()
	{
		return new class {

			public function anonymousClassMethod()
			{
			}
		};
	}

}
