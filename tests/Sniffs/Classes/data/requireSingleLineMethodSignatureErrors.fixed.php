<?php // lint >= 8.0

class A
{
	public function __construct(public int|float $abc, string|false $efg) {
	}

	public function someMethodWithNoReturnType(int $abc, string $efg) {
	}
}

interface B
{
	public function someMethod(int $abc, string $efg) : void;

	public function someMethodWithNoReturnType(int $abc, string $efg);
}
