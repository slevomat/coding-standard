<?php // lint >= 8.0

class A
{
	public function __construct(
		public int $abc,
		protected string $efg,
		bool $aaaaa,
		int $bbbbb,
		string $ccccc,
		float $ddddd,
		array $eeeee
	) {
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
		array|false $eeeee
	);
}
