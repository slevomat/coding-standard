<?php

class A
{
	public function someMethod(
		int $abc,
		string $efg,
		bool $aaaaa,
		int $bbbbb,
		string $ccccc,
		float $ddddd,
		array $eeeee
	) : void {
	}

	public function someMethodWithNoReturnType(
		int $abc,
		string $efg,
		bool $aaaaa,
		int $bbbbb,
		string $ccccc,
		float $ddddd,
		array $eeeee
	) {
	}
}

interface B
{
	public function someMethod(
		int $abc,
		string $efg,
		bool $aaaaa,
		int $bbbbb,
		string $ccccc,
		float $ddddd,
		array $eeeee
	) : void;

	public function someMethodWithNoReturnType(
		int $abc,
		string $efg,
		bool $aaaaa,
		int $bbbbb,
		string $ccccc,
		float $ddddd,
		array $eeeee
	);
}
