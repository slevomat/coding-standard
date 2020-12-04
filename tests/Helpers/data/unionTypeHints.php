<?php // lint >= 8.0

class Whatever
{

	private float|int|false|IntClass|IntInterface $a;

	private StringClass|StringInterface $a;

	public function doSomething(bool|int|false|Anything|Nothing $a): int|false|DateTimeImmutable
	{
	}

	public function doAnything(Something $a): DateTime
	{
	}

}
