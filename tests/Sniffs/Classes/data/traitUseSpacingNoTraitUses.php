<?php

use DateTimeImmutable;

class Whatever
{

	public function doSomething($parameter)
	{
		return function () use ($parameter) {

		};
	}

}
