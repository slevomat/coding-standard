<?php

?><?php

class Whatever
{

	const SOME_CONSTANT = PHP_VERSION;

	public function doSomething(): self
	{
		return new class extends Anything {

			public function doSomethingElse(): Whatever
			{

			}

		};
	}

	public function doSomethingElse()
	{
		return new class extends Whatever {

		};
	}

	public function doAnything()
	{
		return new class implements Whatever {

		};
	}

}

$function = function (): Whatever {

};
