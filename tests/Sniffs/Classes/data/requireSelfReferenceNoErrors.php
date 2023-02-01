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

}

$function = function (): Whatever {

};
