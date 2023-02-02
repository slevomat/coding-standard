<?php // lint >= 8.0

class A
{
	public function __construct(public int $single) {
	}

	public function tooManyParams(
		int $one,
		string $two
	) {
	}
}

interface B
{
	public function tooManyParams(
		int $one,
		string $two,
		string $three
	) : void;

	public function noParam();
}
