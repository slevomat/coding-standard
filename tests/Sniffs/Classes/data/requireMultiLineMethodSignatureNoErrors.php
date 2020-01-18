<?php

function justFunction() {
}


class A
{
	public function noParameters()
	{
	}

	public function shortSignature(string $a)
	{
	}

	public function someMethod(
		int $abc,
		string $efg
	) : void {
	}

	public function someMethodWithNoReturnType(
		int $abc,
		string $efg
	) {
	}

	public function multiLineMethodWithPrecisely120CharsOnSingleline(
		$someHugeVariableNameJustToFillTheSpaceBlah
	) : void {
	}
}

interface B
{
	public function noParameters();

	public function shortSignature(string $a);

	public function someMethod(
		int $abc,
		string $efg
	) : void;

	public function someMethodWithNoReturnType(
		int $abc,
		string $efg
	);

	public function multiLineMethodWithPrecisely120CharsOnSingleline(
		$someHugeVariableNameJustToFillTheSpaceBlah
	) : void;
}
