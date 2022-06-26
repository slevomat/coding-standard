<?php

declare(strict_types=1);

class X
{
	public function a(int $arg,): void
    {
	}

	public function b(
		int $arg
	): void
	{
	}
}

$class = new X();

function a(int $arg) use ($class,): void
{
}

function b(int $arg) use (
	$class
): void
{
}
