<?php // lint >= 8.0

class Whatever
{

	private float|int|false|IntClass|IntInterface $a;

	private StringClass|StringInterface $b;

	public function doSomething(int|false|Anything|Nothing $a): int|false|DateTimeImmutable
	{
	}

	public function doAnything(Something $a): DateTime
	{
	}

}
